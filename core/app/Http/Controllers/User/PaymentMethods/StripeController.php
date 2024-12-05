<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends Controller
{
    /**
     * Process the payment
     *
     * @param Transaction $transaction
     */
    public static function pay(Transaction $transaction)
    {
        if ($transaction->transaction_method == 'order') {
            /* Order payment */

            $restaurant_id = $transaction->user_id;
            $post = Post::find($restaurant_id);
            $postOptions = post_options($post->id);

            $title = "Payment for " . $transaction->product_name;

            $pay_mode = 'one_time';
            $brand_name = $post->title;
            $cancel_url = route('publicView', $post->slug);
            $currency = @$postOptions->currency_code;

            $stripe_secret_key = @$postOptions->restaurant_stripe_secret_key;
            $stripe_publishable_key = @$postOptions->restaurant_stripe_publishable_key;
        } else {
            /* Membership Payment */

            /* Check pay mode */
            if (config('settings.stripe_payment_mode') == 'both') {
                $pay_mode = request()->get('payment_mode', 'one_time');
            } else {
                $pay_mode = config('settings.stripe_payment_mode', 'one_time');
            }

            if ($transaction->frequency == 'LIFETIME') {
                $pay_mode = 'one_time';
            }

            $title = "Payment for " . $transaction->product_name . " Plan (" . $transaction->frequency . ')';

            $brand_name = config('settings.site_title');
            $cancel_url = route('subscription');
            $currency = config('settings.currency_code');

            $stripe_secret_key = config('settings.stripe_secret_key');
            $stripe_publishable_key = config('settings.stripe_publishable_key');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $price = in_array($currency, ['MGA', 'BIF', 'CLP', 'PYG', 'DJF', 'RWF', 'GNF', 'UGX', 'JPY', 'VND', 'VUV', 'XAF', 'KMF', 'KRW', 'XOF', 'XPF'])
            ? $price
            : $price * 100;

        Stripe::setApiKey($stripe_secret_key);

        if ($pay_mode == 'recurring') {
            /* Recurring */

            /* Try to get the product */
            try {
                $stripe_product = \Stripe\Product::retrieve($transaction->product_id);

                /* Check if the plan's name has changed */
                if ($transaction->product_name != $stripe_product->name) {

                    /* Update the product name */
                    try {
                        $stripe_product = \Stripe\Product::update($stripe_product->id, [
                            'name' => $transaction->product_name
                        ]);
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                        quick_alert_error($e->getMessage());
                        return back();
                    }
                }
            } catch (\Exception $e) {
                /* Create the product if not already created */
                try {
                    $stripe_product = \Stripe\Product::create([
                        'id' => $transaction->product_id,
                        'name' => $transaction->product_name
                    ]);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    quick_alert_error($e->getMessage());
                    return back();
                }
            }

            /* Generate the plan id */
            $stripe_plan_id = $transaction->product_id . '_' . $transaction->frequency . '_' . $price . '_' . config('settings.currency_code');

            /* Get the payment plan */
            try {
                $stripe_plan = \Stripe\Plan::retrieve($stripe_plan_id);
            } catch (\Exception $e) {

                /* Create the plan if not already created */
                try {
                    $stripe_plan = \Stripe\Plan::create([
                        'amount' => $price,
                        'interval' => 'day',
                        'interval_count' => $transaction->frequency == 'MONTHLY' ? 30 : 365,
                        'product' => $stripe_product->id,
                        'id' => $stripe_plan_id,
                        'currency' => config('settings.currency_code'),
                    ]);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    quick_alert_error($e->getMessage());
                    return back();
                }
            }

            try {
                $metadata = $transaction->getAttributes();

                $session = Session::create([
                    'cancel_url' => route('subscription'),
                    'success_url' => route('ipn', 'stripe') . '?payment_id={CHECKOUT_SESSION_ID}&pay_mode=' . $pay_mode,
                    'payment_method_types' => ['card'],
                    'subscription_data' => [
                        'items' => [
                            ['plan' => $stripe_plan->id]
                        ],
                        'metadata' => $metadata,
                    ],
                    'metadata' => $metadata,
                ]);

                Log::info(json_encode($metadata));

                /* Delete this transaction, we will create new one for the recurring payment */
                $transaction->delete();

            } catch (\Exception $e) {
                Log::info($e->getMessage());
                quick_alert_error($e->getMessage());
                return back();
            }


        } else {
            /* One Time */
            try {
                $session = Session::create([
                    'line_items' => [[
                        'price_data' => [
                            'product_data' => [
                                'name' => $title,
                                'description' => $title,
                            ],
                            'unit_amount' => $price,
                            'currency' => $currency,
                        ],
                        'quantity' => 1,
                    ]],
                    'payment_method_types' => ['card'],
                    'mode' => 'payment',
                    'cancel_url' => $cancel_url,
                    'success_url' => route('ipn', 'stripe') . '?payment_id={CHECKOUT_SESSION_ID}&pay_mode=' . $pay_mode,
                ]);

                $transaction->update(['payment_id' => $session->id]);

            } catch (\Exception $e) {
                Log::info($e->getMessage());
                quick_alert_error($e->getMessage());
                return back();
            }
        }

        /* redirect to payment gateway page */
        return redirect($session->url);
    }

    /**
     * Handle the IPN
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {
        if ($request->pay_mode == 'one_time') {

            /* One Time */
            try {

                $payment_id = $request->payment_id;
                $transaction = Transaction::where([
                    ['payment_id', $payment_id],
                    ['status', null],
                ])->first();

                if (is_null($transaction)) {
                    quick_alert_error(___('Invalid transaction, please try again.'));
                    return redirect()->route('subscription');
                }

                if($transaction->transaction_method == 'order'){
                    $restaurant_id = $transaction->user_id;

                    $stripe_secret_key = post_options($restaurant_id, 'restaurant_stripe_secret_key');
                } else {
                    $stripe_secret_key = config('settings.stripe_secret_key');
                }

                Stripe::setApiKey($stripe_secret_key);

                $session = Session::retrieve($payment_id);

                if ($session->payment_status == "paid" && $session->status == "complete") {

                    $update = $transaction->update([
                        'transaction_gatway' => 'stripe',
                        'payment_id' => $session->id,
                        'status' => Transaction::STATUS_SUCCESS,
                    ]);
                    if ($update) {
                        if ($transaction->transaction_method == 'order') {
                            /* Order payment */
                            PaymentController::paySuccess($transaction);
                            return;
                        } else {
                            /* Membership Payment */
                            CheckoutController::updateUserPlan($transaction);
                            quick_alert_success(___('Payment successful'));
                        }
                    }
                } else {
                    quick_alert_error(___('Payment failed, please try again.'));
                    return back();
                }
            } catch (\Exception $e) {
                Log::info($e->getMessage());
                quick_alert_error(___('Payment failed, please try again.'));
                return back();
            }

        } else {
            /* Send a success message for recurring payment */
            quick_alert_success(___('Payment successful'));
        }

        return redirect()->route('subscription');
    }

    /**
     * Handle the Webhook
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function webhook(Request $request)
    {
        $stripe_secret_key = config('settings.stripe_secret_key');
        $stripe_publishable_key = config('settings.stripe_publishable_key');

        Stripe::setApiKey($stripe_secret_key);

        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(),
                $request->server('HTTP_STRIPE_SIGNATURE'),
                config('settings.stripe_webhook_secret')
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::info($e->getMessage());

            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            Log::info($e->getMessage());

            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }

        /*  */
        if ($event->type == 'invoice.paid') {

            $session = $event->data->object;

            // Get the metadata
            $metadata = $session->lines->data[0]->metadata ?? ($session->metadata ?? null);

            if($metadata){
                return CheckoutController::processWebhook('stripe', $metadata, $session->id, $session->subscription);
            } else {
                return response()->json([
                    'status' => 400
                ], 400);
            }
        }

        return response()->json([
            'message' => 'successful',
            'status' => 200
        ], 200);
    }
}

<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{

    /**
     * Process the payment
     *
     * @param  Transaction  $transaction
     */
    public static function pay(Transaction $transaction)
    {
        if ($transaction->transaction_method == 'order') {
            /* Order payment */

            $restaurant_id = $transaction->user_id;
            $post = Post::find($restaurant_id);
            $postOptions = post_options($post->id);

            $pay_mode = 'one_time';
            $brand_name = $post->title;
            $cancel_url = route('publicView', $post->slug);
            $currency = @$postOptions->currency_code;

            /* Update paypal credentials */
            config(['settings.paypal_api_client_id' => @$postOptions->restaurant_paypal_api_client_id]);
            config(['settings.paypal_api_secret' => @$postOptions->restaurant_paypal_api_secret]);
            config(['settings.paypal_api_app_id' => @$postOptions->restaurant_paypal_api_app_id]);
            config(['settings.paypal_sandbox_mode' => @$postOptions->restaurant_paypal_sandbox_mode]);
        } else {
            /* Membership Payment */

            /* Check pay mode */
            if (config('settings.paypal_payment_mode') == 'both') {
                $pay_mode = request()->get('payment_mode', 'one_time');
            } else {
                $pay_mode = config('settings.paypal_payment_mode', 'one_time');
            }

            if ($transaction->frequency == 'LIFETIME') {
                $pay_mode = 'one_time';
            }

            $brand_name = config('settings.site_title');
            $cancel_url = route('subscription');
            $currency = config('settings.currency_code');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        try {
            $provider = self::getPaypalProvider();

            if ($pay_mode == 'recurring') {
                /* Recurring */

                $product_id = 'product_'.$transaction->product_id;

                /* Try to get the product */
                try {
                    $paypal_product = $provider->showProductDetails($product_id);
                } catch (\Exception $e) {
                    /* Create the product if not already created */
                    try {
                        $paypal_product = $provider->createProduct([
                            'id' => $product_id,
                            'name' => $transaction->product_name,
                            'description' => $transaction->product_name,
                            'type' => 'SERVICE'
                        ]);
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                        quick_alert_error($e->getMessage());
                        return back();
                    }
                }

                /* Generate the plan id */
                $paypal_plan_name = 'plan_'.$transaction->product_id.'_'.$transaction->frequency.'_'.$price.'_'.config('settings.currency_code');

                /* Create the plan */
                try {
                    $paypal_plan = $provider->createPlan([
                        'product_id' => $paypal_product['id'],
                        'name' => $paypal_plan_name,
                        'status' => 'ACTIVE',
                        'billing_cycles' => [
                            [
                                'frequency' => [
                                    'interval_unit' => 'DAY',
                                    'interval_count' => $transaction->frequency == 'MONTHLY' ? 30 : 365,
                                ],
                                'tenure_type' => 'REGULAR',
                                'sequence' => 1,
                                'total_cycles' => 0,
                                'pricing_scheme' => [
                                    'fixed_price' => [
                                        'value' => $price,
                                        'currency_code' => config('settings.currency_code'),
                                    ],
                                ]
                            ]
                        ],
                        'payment_preferences' => [
                            'auto_bill_outstanding' => true,
                            'payment_failure_threshold' => 0,
                        ],
                    ]);
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    quick_alert_error($e->getMessage());
                    return back();
                }

                /* Create the subscription */
                try {
                    $metadata = $transaction->getAttributes();

                    $paypal_subscription = $provider->createSubscription([
                        'plan_id' => $paypal_plan['id'],
                        'application_context' => [
                            'brand_name' => config('settings.site_title'),
                            'shipping_preference' => 'NO_SHIPPING',
                            'user_action' => 'SUBSCRIBE_NOW',
                            'payment_method' => [
                                'payer_selected' => 'PAYPAL',
                                'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                            ],
                            'return_url' => route('ipn', 'paypal').'?pay_mode='.$pay_mode,
                            'cancel_url' => route('subscription')
                        ],
                        'custom_id' => http_build_query($metadata)
                    ]);

                    $redirect_url = $paypal_subscription['links'][0]['href'];

                    /* Delete this transaction, we will create new one for the recurring payment */
                    $transaction->delete();

                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                    quick_alert_error($e->getMessage());
                    return back();
                }

            } else {
                /* One Time */

                $response = $provider->createOrder([
                    "intent" => "CAPTURE",
                    'application_context' => [
                        'brand_name' => $brand_name,
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'PAY_NOW',
                        "return_url" => route('ipn', 'paypal').'?pay_mode='.$pay_mode,
                        "cancel_url" => $cancel_url
                    ],
                    "purchase_units" => [
                        0 => [
                            "amount" => [
                                "currency_code" => $currency,
                                "value" => number_format((float) $price, 2)
                            ]
                        ]
                    ]
                ]);

                if (isset($response['id']) && $response['id'] != null) {
                    // redirect to approve href
                    $redirect_url = $response['links'][1]['href'];

                    $transaction->update(['payment_id' => $response['id']]);
                } else {
                    Log::info(json_encode($response));
                    quick_alert_error(!empty($response['error']['message'])
                        ? ___('Payment failed').' : '.$response['error']['message']
                        : ___('Payment failed, check the credentials.'));
                    return back();
                }
            }

            /* redirect to payment gateway page */
            return redirect($redirect_url);

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            quick_alert_error($e->getMessage());
            return back();
        }
    }

    /**
     * Get paypal provider
     *
     * @return PayPalClient
     */
    public static function getPaypalProvider()
    {
        $paypal_api_client_id = config('settings.paypal_api_client_id');
        $paypal_api_secret = config('settings.paypal_api_secret');
        $paypal_api_app_id = config('settings.paypal_api_app_id');
        $paypal_sandbox_mode = config('settings.paypal_sandbox_mode');

        if ($paypal_sandbox_mode == 'Yes') {
            $config = [
                'mode' => 'sandbox',
                'sandbox' => [
                    'client_id' => $paypal_api_client_id,
                    'client_secret' => $paypal_api_secret,
                    'app_id' => 'APP-80W284485P519543T',
                ],

                'payment_action' => 'Sale',
                'currency' => config('settings.currency_code'),
                'notify_url' => '',
                'validate_ssl' => false,
                'locale' => get_lang()
            ];
        } else {
            $config = [
                'mode' => 'live',
                'live' => [
                    'client_id' => $paypal_api_client_id,
                    'client_secret' => $paypal_api_secret,
                    'app_id' => $paypal_api_app_id,
                ],

                'payment_action' => 'Sale',
                'currency' => config('settings.currency_code'),
                'notify_url' => '',
                'validate_ssl' => true,
                'locale' => get_lang()
            ];
        }

        $provider = new PayPalClient($config);
        $provider->getAccessToken();
        return $provider;
    }

    /**
     * Handle the IPN
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {
        if ($request->pay_mode == 'one_time') {

            try {
                $transaction = Transaction::where([
                    ['payment_id', $request['token']],
                    ['status', null],
                ])->first();

                if (is_null($transaction)) {
                    quick_alert_error(___('Invalid transaction, please try again.'));
                    return redirect()->route('subscription');
                }

                if ($transaction->transaction_method == 'order') {
                    $restaurant_id = $transaction->user_id;

                    /* Update paypal credentials */
                    config([
                        'settings.paypal_api_client_id' => post_options($restaurant_id,
                            'restaurant_paypal_api_client_id')
                    ]);
                    config([
                        'settings.paypal_api_secret' => post_options($restaurant_id, 'restaurant_paypal_api_secret')
                    ]);
                    config([
                        'settings.paypal_api_app_id' => post_options($restaurant_id, 'restaurant_paypal_api_app_id')
                    ]);
                    config([
                        'settings.paypal_sandbox_mode' => post_options($restaurant_id, 'restaurant_paypal_sandbox_mode')
                    ]);
                }

                $provider = self::getPaypalProvider();

                $response = $provider->capturePaymentOrder($request['token']);

                if (isset($response['status']) && $response['status'] == 'COMPLETED') {

                    $update = $transaction->update([
                        'transaction_gatway' => 'paypal',
                        'payment_id' => $request['token'],
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
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function webhook(Request $request)
    {
        $payload = json_decode($request->getContent());
        if ($payload) {

            try {
                /* Recurring payment */
                if ($payload->event_type == 'PAYMENT.SALE.COMPLETED') {
                    $provider = self::getPaypalProvider();

                    /* Get subscription details */
                    $response = $provider->showSubscriptionDetails($payload->resource->billing_agreement_id);

                    if ($response) {
                        // Get the metadata
                        parse_str($payload->resource->custom_id ?? ($payload->resource->custom ?? null), $metadata);
                        if ($metadata) {
                            $metadata = array_to_object($metadata);
                            return CheckoutController::processWebhook('paypal', $metadata, $payload->resource->id,
                                $payload->resource->billing_agreement_id);
                        } else {
                            return response()->json([
                                'status' => 400
                            ], 400);
                        }
                    }

                }


            } catch (\Exception $e) {
                Log::info($e->getMessage());

                return response()->json([
                    'message' => $e->getMessage(),
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

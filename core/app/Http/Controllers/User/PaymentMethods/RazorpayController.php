<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayController extends Controller
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

            $title = "Payment for " . $transaction->product_name;

            $brand_name = $post->title;
            $cancel_url = route('publicView', $post->slug);
            $currency = @$postOptions->currency_code;

            $razorpay_api_key = @$postOptions->restaurant_razorpay_api_key;
            $razorpay_secret_key = @$postOptions->restaurant_razorpay_secret_key;

            $receipt = $transaction->product_id;
            $name = $transaction->details->customer_name;
            $email = '';
            $color = $post->color;

            /* Update currency */
            config(['settings.currency_sign' => @$postOptions->currency_sign]);
            config(['settings.currency_pos' => @$postOptions->currency_pos]);
            config(['settings.currency_code' => @$postOptions->currency_code]);

        } else {
            /* Membership Payment */

            $title = "Payment for " . $transaction->product_name . " Plan (" . $transaction->frequency . ')';

            $brand_name = config('settings.site_title');
            $cancel_url = route('subscription');
            $currency = config('settings.currency_code');

            $razorpay_api_key = config('settings.razorpay_api_key');
            $razorpay_secret_key = config('settings.razorpay_secret_key');

            $receipt = $transaction->id;
            $name = request()->user()->name;
            $email = request()->user()->email;
            $color = config('settings.theme_color');
        }

        $price = $transaction->amount * 100; // convert to paisa

        try {
            $api = new Api($razorpay_api_key, $razorpay_secret_key);
            $order = $api->order->create([
                'receipt' => (string) $receipt,
                'amount' => $price,
                'currency' => $currency,
                'payment_capture' => '0',
            ]);

            $order_id = $order['id'];
            $details = [
                'key' => $razorpay_api_key,
                'name' => $brand_name,
                'currency' => $currency,
                'amount' => $price,
                'order_id' => $order_id,
                'description' => $title,
                'prefill.name' => $name,
                'prefill.email' => $email,
                'theme.color' => $color,
                'buttontext' => ___('Pay Now'),
                'image' => '',
            ];

            $transaction->update(['payment_id' => $order_id]);

            /* display payment gateway form */
            return view(active_theme()."user.gateways.razorpay", compact('details', 'transaction', 'cancel_url', 'color'));
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            quick_alert_error($e->getMessage());
            return back()->withInput();
        }
    }

    /**
     * Handle the IPN
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {
        try {
            $order_id = $request->razorpay_order_id;

            $transaction = Transaction::where([
                ['payment_id', $order_id],
                ['status', null],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(___('Invalid transaction, please try again.'));
                return redirect()->route('subscription');
            }

            if($transaction->transaction_method == 'order'){
                $restaurant_id = $transaction->user_id;

                $razorpay_secret_key = post_options($restaurant_id, 'restaurant_razorpay_secret_key');
            } else {
                $razorpay_secret_key = config('settings.razorpay_secret_key');
            }

            $signature = hash_hmac('sha256', $request->razorpay_order_id."|".$request->razorpay_payment_id,
                $razorpay_secret_key);

            if ($signature == $request->razorpay_signature) {
                $update = $transaction->update([
                    'transaction_gatway' => 'razorpay',
                    'payment_id' => $request->razorpay_payment_id,
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

        return redirect()->route('subscription');
    }
}

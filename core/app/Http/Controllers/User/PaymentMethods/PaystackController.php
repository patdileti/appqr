<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaystackController extends Controller
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
            $validator = Validator::make(request()->all(), [
                'email' => 'required|email'
            ]);
            if ($validator->fails()) {
                quick_alert_error($validator->errors()->first());
                return back()->withInput();
            }

            $restaurant_id = $transaction->user_id;
            $post = Post::find($restaurant_id);
            $postOptions = post_options($post->id);

            $title = "Payment for ".$transaction->product_name;

            $currency = @$postOptions->currency_code;
            $email = request()->get('email');
            $color = $post->color;
            $cancel_url = route('publicView', $post->slug);

            $paystack_public_key = @$postOptions->restaurant_paystack_public_key;

            /* Update currency */
            config(['settings.currency_sign' => @$postOptions->currency_sign]);
            config(['settings.currency_pos' => @$postOptions->currency_pos]);
            config(['settings.currency_code' => @$postOptions->currency_code]);
        } else {
            /* Membership Payment */

            $title = "Payment for ".$transaction->product_name." Plan (".$transaction->frequency.')';

            $currency = config('settings.currency_code');
            $email = request()->user()->email;
            $color = config('settings.theme_color');
            $cancel_url = route('subscription');

            $paystack_public_key = config('settings.paystack_public_key');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $details = [
            'key' => $paystack_public_key,
            'email' => $email,
            'currency' => $currency,
            'amount' => $price * 100,
            'ref' => $transaction->id,
            'title' => $title,
            'description' => $title,
        ];

        /* display payment gateway form */
        return view(active_theme()."user.gateways.paystack", compact('details', 'transaction', 'color', 'cancel_url'));
    }

    /**
     * Handle the IPN
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {
        $reference = $request->get('reference');

        $transaction = Transaction::where([
            ['id', $reference],
            ['status', null],
            ['payment_id', null],
        ])->first();

        if (is_null($transaction)) {
            quick_alert_error(___('Invalid transaction, please try again.'));
            return redirect()->route('subscription');
        }

        if ($transaction->transaction_method == 'order') {
            $restaurant_id = $transaction->user_id;

            $paystack_secret_key = post_options($restaurant_id, 'restaurant_paystack_secret_key');
        } else {
            $paystack_secret_key = config('settings.paystack_secret_key');
        }

        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/'.$reference;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer '.$paystack_secret_key]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $result = json_decode($response, true);
            if ($result) {
                if ($result['data']) {
                    if ($result['data']['status'] == 'success') {

                        $update = $transaction->update([
                            'transaction_gatway' => 'paystack',
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
                        Log::info('Paystack Payment failed: '.$result['data']['gateway_response']);
                        quick_alert_error(___('Payment failed').' : '.$result['data']['gateway_response']);
                        return back();
                    }
                } else {
                    Log::info('Paystack Payment failed: '.$result['message']);
                    quick_alert_error(___('Payment failed').' : '.$result['message']);
                    return back();
                }
            } else {
                quick_alert_error(___('Payment failed, please try again.'));
                return back();
            }
        } else {
            quick_alert_error(___('Payment failed, please try again.'));
            return back();
        }

        return redirect()->route('subscription');
    }
}

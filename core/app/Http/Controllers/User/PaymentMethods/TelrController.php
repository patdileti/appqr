<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelrController extends Controller
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
            $currency = @$postOptions->currency_code;

            $telr_store_id = @$postOptions->restaurant_telr_store_id;
            $telr_authkey = @$postOptions->restaurant_telr_authkey;
            $telr_sandbox_mode = @$postOptions->restaurant_telr_sandbox_mode;

        } else {
            /* Membership Payment */

            $title = "Payment for " . $transaction->product_name . " Plan (" . $transaction->frequency . ')';
            $currency = config('settings.currency_code');

            $telr_store_id = config('settings.telr_store_id');
            $telr_authkey = config('settings.telr_authkey');
            $telr_sandbox_mode = config('settings.telr_sandbox_mode');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://secure.telr.com/gateway/order.json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'method' => 'create',
                    'store' => $telr_store_id,
                    'authkey' => $telr_authkey,
                    'framed' => 0,
                    'order' => [
                        'cartid' => $transaction->id,
                        'test' => $telr_sandbox_mode == 'test' ? 1 : 0,
                        'amount' => $price,
                        'currency' => $currency,
                        'description' => $title,
                    ],
                    'return' => [
                        'authorised' => route('ipn', 'telr').'?order_id='.$transaction->id,
                        'declined' => route('subscription'),
                        'cancelled' => route('subscription')
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "accept: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!empty($err)) {
                Log::info($err);
                quick_alert_error($err);
                return back()->withInput();
            }

            $result = json_decode($response, true);

            if (array_key_exists("error", $result)) {
                Log::info($result['error']['message']);
                quick_alert_error($result['error']['message']);
                return back()->withInput();
            }

            $transaction->update(['payment_id' => $result['order']['ref']]);

            /* redirect to payment gateway page */
            return redirect($result['order']['url']);

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
            $id = $request->get('order_id');

            $transaction = Transaction::where([
                ['id', $id],
                ['status', null],
                ['payment_id', '!=', null],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(___('Invalid transaction, please try again.'));
                return redirect()->route('subscription');
            }

            if ($transaction->transaction_method == 'order') {
                $restaurant_id = $transaction->user_id;

                $telr_store_id = post_options($restaurant_id, 'restaurant_telr_store_id');
                $telr_authkey = post_options($restaurant_id, 'restaurant_telr_authkey');
            } else {
                $telr_store_id = config('settings.telr_store_id');
                $telr_authkey = config('settings.telr_authkey');
            }

            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://secure.telr.com/gateway/order.json",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'method' => 'check',
                    'store' => $telr_store_id,
                    'authkey' => $telr_authkey,
                    'order' => [
                        'ref' => $transaction->payment_id
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "Content-Type: application/json",
                    "accept: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if (!empty($err)) {
                Log::info($err);
                quick_alert_error($err);
                return back();
            } else {
                $result = json_decode($response, true);

                if (array_key_exists("error", $result)) {
                    Log::info($result['error']['message']);
                    quick_alert_error($result['error']['message']);
                    return back();
                } else {

                    if ($result['order']['status']['code'] == 3) {
                        $update = $transaction->update([
                            'transaction_gatway' => 'telr',
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
                }
            }

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            quick_alert_error(___('Payment failed, please try again.'));
            return back();
        }

        return redirect()->route('subscription');
    }
}

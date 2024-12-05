<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaytabsController extends Controller
{

    const BASE_URLS = [
        'ARE' => [
            'title' => 'United Arab Emirates',
            'endpoint' => 'https://secure.paytabs.com/'
        ],
        'SAU' => [
            'title' => 'Saudi Arabia',
            'endpoint' => 'https://secure.paytabs.sa/'
        ],
        'OMN' => [
            'title' => 'Oman',
            'endpoint' => 'https://secure-oman.paytabs.com/'
        ],
        'JOR' => [
            'title' => 'Jordan',
            'endpoint' => 'https://secure-jordan.paytabs.com/'
        ],
        'EGY' => [
            'title' => 'Egypt',
            'endpoint' => 'https://secure-egypt.paytabs.com/'
        ],
        'IRQ' => [
            'title' => 'Iraq',
            'endpoint' => 'https://secure-iraq.paytabs.com/'
        ],
        'PSE' => [
            'title' => 'Palestine',
            'endpoint' => 'https://secure-palestine.paytabs.com/'
        ],
        'GLOBAL' => [
            'title' => 'Global',
            'endpoint' => 'https://secure-global.paytabs.com/'
        ]
    ];

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

            $paytabs_profile_id = @$postOptions->restaurant_paytabs_profile_id;
            $paytabs_secret_key = @$postOptions->restaurant_paytabs_secret_key;
            $paytabs_region = @$postOptions->restaurant_paytabs_region;

        } else {
            /* Membership Payment */

            $title = "Payment for " . $transaction->product_name . " Plan (" . $transaction->frequency . ')';

            $currency = config('settings.currency_code');

            $paytabs_profile_id = config('settings.paytabs_profile_id');
            $paytabs_secret_key = config('settings.paytabs_secret_key');
            $paytabs_region = config('settings.paytabs_region');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $data = array(
            'profile_id' => $paytabs_profile_id,
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => strval($transaction->id),
            'cart_description' => $title,
            'cart_currency' => $currency,
            'cart_amount' => $price,
            'callback' => route('ipn', 'ccavenue'),
            'return' => route('ipn', 'ccavenue'),
            "hide_shipping" => true
        );
        $data = json_encode($data);

        $base_url = self::BASE_URLS[$paytabs_region]['endpoint'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url.'payment/request');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $headers = array();
        $headers[] = 'Authorization: '.$paytabs_secret_key;
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $request = curl_exec($ch);
        $result = json_decode($request, true);
        curl_close($ch);

        if (isset($result['redirect_url'])) {
            $transaction->update(['payment_id' => $result['tran_ref']]);

            /* redirect to payment gateway page */
            return redirect($result['redirect_url']);
        } else {
            Log::info($result['message']);
            quick_alert_error($result['message']);
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
        $response = json_decode($request->getContent());
        $id = $response->cart_id;

        $transaction = Transaction::where([
            ['id', $id],
            ['status', null],
            ['payment_id', '!=', null],
        ])->first();

        if (is_null($transaction)) {
            quick_alert_error(___('Invalid transaction, please try again.'));
            return redirect()->route('subscription');
        }

        $tran_ref = $transaction->payment_id;

        if ($transaction->transaction_method == 'order') {
            $restaurant_id = $transaction->user_id;

            $paytabs_profile_id = post_options($restaurant_id, 'restaurant_paytabs_profile_id');
            $paytabs_secret_key = post_options($restaurant_id, 'restaurant_paytabs_secret_key');
            $paytabs_region = post_options($restaurant_id, 'restaurant_paytabs_region');
        } else {
            $paytabs_profile_id = config('settings.paytabs_profile_id');
            $paytabs_secret_key = config('settings.paytabs_secret_key');
            $paytabs_region = config('settings.paytabs_region');
        }

        $data = array(
            'profile_id' => $paytabs_profile_id,
            'tran_ref' => $tran_ref
        );
        $data = json_encode($data);

        $base_url = self::BASE_URLS[$paytabs_region]['endpoint'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $base_url.'payment/query');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $headers = array();
        $headers[] = 'Authorization: '.$paytabs_secret_key.'';
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $request = curl_exec($ch);
        $result = json_decode($request, true);
        curl_close($ch);

        if ($result['payment_result']['response_status'] == "A") {
            $update = $transaction->update([
                'transaction_gatway' => 'paytabs',
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

        return redirect()->route('subscription');
    }

}

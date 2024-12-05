<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;

class CcavenueController extends Controller
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

            $merchant_id = @$postOptions->restaurant_ccavenue_merchant_key;
            $access_code = @$postOptions->restaurant_ccavenue_access_code;
            $working_key = @$postOptions->restaurant_ccavenue_working_key;

            $return_url = route('ipn', 'ccavenue').'?order_id='.$transaction->id;
            $cancel_url = route('publicView', $post->slug);

            $currency = @$postOptions->currency_code;
        } else {
            /* Membership Payment */

            $merchant_id = config('settings.CCAVENUE_MERCHANT_KEY');
            $access_code = config('settings.CCAVENUE_ACCESS_CODE');
            $working_key = config('settings.CCAVENUE_WORKING_KEY');

            $return_url = route('ipn', 'ccavenue');
            $cancel_url = route('subscription');

            $currency = config('settings.currency_code');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $merchant_data = http_build_query([
            'merchant_id' => $merchant_id,
            'order_id' => $transaction->id,
            'amount' => $price,
            'currency' => $currency,
            'redirect_url' => $return_url,
            'cancel_url' => $cancel_url,
            'language' => get_lang()
        ]);
        $encrypted_data = static::encrypt($merchant_data, $working_key);

        $data = [
            'encRequest' => $encrypted_data,
            'access_code' => $access_code
        ];

        $url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

        /* redirect to payment gatway */
        return view(active_theme()."user.gateways.redirect-form", compact('url', 'data'));

    }

    /**
     * Handle the IPN
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {

        if ($request->has('order_id')) {
            /* Order payment */

            $transaction = Transaction::find($request->get('order_id'));
            if (is_null($transaction)) {
                quick_alert_error(___('Invalid transaction, please try again.'));
                return back();
            }

            if ($transaction->transaction_method == 'order') {
                $restaurant_id = $transaction->user_id;

                $working_key = post_options($restaurant_id,
                    'restaurant_ccavenue_working_key');
            }
        } else {
            $working_key = config('settings.CCAVENUE_WORKING_KEY');
        }

        $encResponse = $_POST["encResp"];            //This is the response sent by the CCAvenue Server
        $rcvdString = static::decrypt($encResponse,
            $working_key);
        $decryptValues = explode('&', $rcvdString);
        $dataSize = sizeof($decryptValues);

        for ($i = 0; $i < $dataSize; $i++) {
            $information = explode('=', $decryptValues[$i]);
            $responseMap[$information [0]] = $information [1];
        }

        if ($responseMap['order_status'] === "Success") {

            $id = $responseMap['order_id'];

            $transaction = Transaction::where([
                ['id', $id],
                ['status', null],
                ['payment_id', null],
            ])->first();

            if (is_null($transaction)) {
                quick_alert_error(___('Invalid transaction, please try again.'));
                return redirect()->route('subscription');
            }

            $update = $transaction->update([
                'transaction_gatway' => 'ccavenue',
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

    /* Helper Functions */

    /*
    * @param1 : Plain String
    * @param2 : Working key provided by CCAvenue
    * @return : Decrypted String
    */
    private static function encrypt($plainText, $key)
    {
        $key = static::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d,
            0x0e, 0x0f);
        $openMode = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        $encryptedText = bin2hex($openMode);
        return $encryptedText;
    }

    /*
    * @param1 : Encrypted String
    * @param2 : Working key provided by CCAvenue
    * @return : Plain String
    */
    private static function decrypt($encryptedText, $key)
    {
        $key = static::hextobin(md5($key));
        $initVector = pack("C*", 0x00, 0x01, 0x02, 0x03, 0x04, 0x05, 0x06, 0x07, 0x08, 0x09, 0x0a, 0x0b, 0x0c, 0x0d,
            0x0e, 0x0f);
        $encryptedText = static::hextobin($encryptedText);
        $decryptedText = openssl_decrypt($encryptedText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $initVector);
        return $decryptedText;
    }

    private static function hextobin($hexString)
    {
        $length = strlen($hexString);
        $binString = "";
        $count = 0;
        while ($count < $length) {
            $subString = substr($hexString, $count, 2);
            $packedString = pack("H*", $subString);
            if ($count == 0) {
                $binString = $packedString;
            } else {
                $binString .= $packedString;
            }

            $count += 2;
        }
        return $binString;
    }
}

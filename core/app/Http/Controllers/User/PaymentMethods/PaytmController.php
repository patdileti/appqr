<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaytmController extends Controller
{

    /**
     * Process the payment
     *
     * @param  Transaction  $transaction
     */
    public static function pay(Transaction $transaction)
    {
        $PAYTM_TXN_URL = 'https://securegw.paytm.in/theia/processTransaction';
        $PAYTM_TXN_URL_SANDBOX = 'https://securegw-stage.paytm.in/theia/processTransaction';

        $price = number_format((float) $transaction->amount, 2, '.', '');

        if ($transaction->transaction_method == 'order') {
            /* Order payment */

            $restaurant_id = $transaction->user_id;
            $post = Post::find($restaurant_id);
            $postOptions = post_options($post->id);

            $PAYTM_MERCHANT_KEY = @$postOptions->restaurant_paytm_merchant_key;
            $PAYTM_MERCHANT_MID = @$postOptions->restaurant_paytm_merchant_mid;
            $PAYTM_MERCHANT_WEBSITE = @$postOptions->restaurant_paytm_merchant_website;
            $PAYTM_SANDBOX = @$postOptions->restaurant_paytm_sandbox_mode;

            $data = array(
                "MID" => $PAYTM_MERCHANT_MID,
                "WEBSITE" => $PAYTM_MERCHANT_WEBSITE,
                "ORDER_ID" => $transaction->id,
                "CUST_ID" => $restaurant_id,
                "INDUSTRY_TYPE_ID" => 'Retail',
                "CHANNEL_ID" => 'WEB',
                "CALLBACK_URL" => route('ipn', 'paytm'),
                "TXN_AMOUNT" => $price
            );
        } else {
            /* Membership Payment */

            $PAYTM_MERCHANT_KEY = config('settings.PAYTM_MERCHANT_KEY');
            $PAYTM_MERCHANT_MID = config('settings.PAYTM_MERCHANT_MID');
            $PAYTM_MERCHANT_WEBSITE = config('settings.PAYTM_MERCHANT_WEBSITE');
            $PAYTM_SANDBOX = config('settings.PAYTM_ENVIRONMENT');

            $user = request()->user();
            $data = array(
                "MID" => $PAYTM_MERCHANT_MID,
                "WEBSITE" => $PAYTM_MERCHANT_WEBSITE,
                "ORDER_ID" => $transaction->id,
                "CUST_ID" => $user->id,
                "INDUSTRY_TYPE_ID" => 'Retail',
                "CHANNEL_ID" => 'WEB',
                "CALLBACK_URL" => route('ipn', 'paytm'),
                "EMAIL" => $user->email,
                "VERIFIED_BY" => 'EMAIL',
                "IS_USER_VERIFIED" => 'YES',
                "TXN_AMOUNT" => $price
            );
        }

        //Here checksum string will return by getChecksumFromArray() function.
        $checkSum = static::getChecksumFromArray($data, $PAYTM_MERCHANT_KEY);
        $data['CHECKSUMHASH'] = $checkSum;

        $url = ($PAYTM_SANDBOX == 'TEST') ? $PAYTM_TXN_URL_SANDBOX : $PAYTM_TXN_URL;

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
        $transaction = Transaction::where([
            ['id', $_POST['ORDERID']],
            ['status', null],
            ['payment_id', null],
        ])->first();

        if (is_null($transaction)) {
            quick_alert_error(___('Invalid transaction, please try again.'));
            return redirect()->route('subscription');
        }

        $PAYTM_STATUS_QUERY_NEW_URL_SANDBOX = 'https://securegw-stage.paytm.in/merchant-status/getTxnStatus';
        $PAYTM_STATUS_QUERY_NEW_URL = 'https://securegw.paytm.in/merchant-status/getTxnStatus';

        if ($transaction->transaction_method == 'order') {
            $restaurant_id = $transaction->user_id;

            $PAYTM_MERCHANT_KEY = post_options($restaurant_id, 'restaurant_paytm_merchant_key');
            $PAYTM_MERCHANT_MID = post_options($restaurant_id, 'restaurant_paytm_merchant_mid');
            $PAYTM_MERCHANT_WEBSITE = post_options($restaurant_id, 'restaurant_paytm_merchant_website');
            $PAYTM_SANDBOX = post_options($restaurant_id, 'restaurant_paytm_sandbox_mode');
        } else {
            $PAYTM_MERCHANT_KEY = config('settings.PAYTM_MERCHANT_KEY');
            $PAYTM_MERCHANT_MID = config('settings.PAYTM_MERCHANT_MID');
            $PAYTM_MERCHANT_WEBSITE = config('settings.PAYTM_MERCHANT_WEBSITE');
            $PAYTM_SANDBOX = config('settings.PAYTM_ENVIRONMENT');
        }

        if ($_POST['RESPCODE'] == "01") {
            if (static::verifychecksum_e($_POST, $PAYTM_MERCHANT_KEY, $_POST['CHECKSUMHASH']) === "TRUE") {

                $requestParamList = array("MID" => $PAYTM_MERCHANT_MID, "ORDERID" => $_POST['ORDERID']);
                $StatusCheckSum = static::getChecksumFromArray($requestParamList, $PAYTM_MERCHANT_KEY);
                $requestParamList['CHECKSUMHASH'] = urlencode($StatusCheckSum);

                $url = ($PAYTM_SANDBOX == 'TEST') ? $PAYTM_STATUS_QUERY_NEW_URL_SANDBOX : $PAYTM_STATUS_QUERY_NEW_URL;

                $responseParamList = static::callNewAPI($url, $requestParamList);

                if ($responseParamList['STATUS'] == 'TXN_SUCCESS') {
                    $update = $transaction->update([
                        'transaction_gatway' => 'paytm',
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

            } else {
                quick_alert_error(___('Payment failed, please try again.'));
                return back();
            }
        } else {
            Log::info('Paytm Payment failed: '.$_POST['RESPMSG']);
            quick_alert_error(___('Payment failed').' : '.$_POST['RESPMSG']);
            return back();
        }

        return redirect()->route('subscription');
    }

    /* Paytm Functions */
    private static function getChecksumFromArray($arrayList, $key, $sort = 1)
    {
        if ($sort != 0) {
            ksort($arrayList);
        }
        $str = static::getArray2Str($arrayList);
        $salt = static::generateSalt_e(4);
        $finalString = $str."|".$salt;
        $hash = hash("sha256", $finalString);
        $hashString = $hash.$salt;
        $checksum = static::encrypt_e($hashString, $key);
        return $checksum;
    }

    private static function getArray2Str($arrayList)
    {
        $findme = 'REFUND';
        $findmepipe = '|';
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            $pos = strpos($value, $findme);
            $pospipe = strpos($value, $findmepipe);
            if ($pos !== false || $pospipe !== false) {
                continue;
            }

            if ($flag) {
                $paramStr .= static::checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|".static::checkString_e($value);
            }
        }
        return $paramStr;
    }

    private static function checkString_e($value)
    {
        if ($value == 'null' || $value == 'NULL') {
            $value = '';
        }
        return $value;
    }

    private static function generateSalt_e($length)
    {
        $random = "";
        srand((double) microtime() * 1000000);

        $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
        $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
        $data .= "0FGH45OP89";

        for ($i = 0; $i < $length; $i++) {
            $random .= substr($data, (rand() % (strlen($data))), 1);
        }

        return $random;
    }

    private static function encrypt_e($input, $ky)
    {
        $key = html_entity_decode($ky);
        $iv = "@@@@&&&&####$$$$";
        $data = openssl_encrypt($input, "AES-128-CBC", $key, 0, $iv);
        return $data;
    }

    private static function verifychecksum_e($arrayList, $key, $checksumvalue)
    {
        $arrayList = static::removeCheckSumParam($arrayList);
        ksort($arrayList);
        $str = static::getArray2StrForVerify($arrayList);
        $paytm_hash = static::decrypt_e($checksumvalue, $key);
        $salt = substr($paytm_hash, -4);

        $finalString = $str."|".$salt;

        $website_hash = hash("sha256", $finalString);
        $website_hash .= $salt;

        $validFlag = "FALSE";
        if ($website_hash == $paytm_hash) {
            $validFlag = "TRUE";
        } else {
            $validFlag = "FALSE";
        }
        return $validFlag;
    }

    private static function removeCheckSumParam($arrayList)
    {
        if (isset($arrayList["CHECKSUMHASH"])) {
            unset($arrayList["CHECKSUMHASH"]);
        }
        return $arrayList;
    }

    private static function getArray2StrForVerify($arrayList)
    {
        $paramStr = "";
        $flag = 1;
        foreach ($arrayList as $key => $value) {
            if ($flag) {
                $paramStr .= static::checkString_e($value);
                $flag = 0;
            } else {
                $paramStr .= "|".static::checkString_e($value);
            }
        }
        return $paramStr;
    }

    private static function decrypt_e($crypt, $ky)
    {
        $key = html_entity_decode($ky);
        $iv = "@@@@&&&&####$$$$";
        $data = openssl_decrypt($crypt, "AES-128-CBC", $key, 0, $iv);
        return $data;
    }

    private static function callNewAPI($apiURL, $requestParamList)
    {
        $jsonResponse = "";
        $responseParamList = array();
        $postData = 'JsonData='.json_encode($requestParamList, JSON_UNESCAPED_SLASHES);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $apiURL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $jsonResponse = curl_exec($ch);
        $responseParamList = json_decode($jsonResponse, true);
        return $responseParamList;
    }
}

<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaddleController extends Controller
{

    /**
     * Process the payment
     *
     * @param  Transaction  $transaction
     */
    public static function pay(Transaction $transaction)
    {
        $title = "Payment for ".$transaction->product_name." Plan (".$transaction->frequency.')';

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $paddle_sandbox_mode = config('settings.paddle_sandbox_mode');
        $paddle_vendor_id = config('settings.paddle_vendor_id');
        $paddle_api_key = config('settings.paddle_api_key');
        $paddle_public_key = config('settings.paddle_public_key');

        $metadata = $transaction->getAttributes();

        $params = [
            'vendor_id' => $paddle_vendor_id,
            'vendor_auth_code' => $paddle_api_key,
            'title' => $title,
            'webhook_url' => url('webhook/paddle'),
            'prices' => [config('settings.currency_code') . ':' . $price],
            'customer_email' => request()->user()->email,
            'passthrough' => json_encode($metadata),
            'return_url' => route('ipn', 'paddle'),
            'image_url' => '',
            'quantity_variable' => 0,
        ];

        $ch = curl_init();
        if($paddle_sandbox_mode == 'Yes'){
            curl_setopt($ch, CURLOPT_URL, "https://sandbox-vendors.paddle.com/api/2.0/product/generate_pay_link");
        } else {
            curl_setopt($ch, CURLOPT_URL, "https://vendors.paddle.com/api/2.0/product/generate_pay_link");
        }

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $request = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($request, true);

        if(!empty($result['response']['url'])) {
            $redirect_url = $result['response']['url'];

            /* display payment gateway form */
            return view(active_theme()."user.gateways.paddle", compact('redirect_url', 'transaction'));
        } else {
            Log::info($result['error']['message']);
            quick_alert_error($result['error']['message']);
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
        /* Send a success message for recurring payment */
        quick_alert_success(___('Your payment is processing.'));

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
        if(empty($_POST)) {
            return response()->json([
                'status' => 400
            ], 400);
        }

        $public_key = openssl_get_publickey(config('settings.paddle_public_key'));

        $signature = base64_decode($_POST['p_signature']);

        $fields = $_POST;
        unset($fields['p_signature']);

        ksort($fields);
        foreach($fields as $k => $v) {
            if(!in_array(gettype($v), array('object', 'array'))) {
                $fields[$k] = "$v";
            }
        }
        $data = serialize($fields);

        $verification = openssl_verify($data, $signature, $public_key, OPENSSL_ALGO_SHA1);

        if(!$verification) {
            return response()->json([
                'message' => 'Invalid signature verification.',
                'status' => 400
            ], 400);
        }

        /* Start getting the payment details */
        $payment_id = $_POST['p_order_id'];
        $metadata = json_decode($_POST['passthrough']);

        if($metadata){
            return CheckoutController::processWebhook('paddle', $metadata, $payment_id, null);
        } else {
            return response()->json([
                'status' => 400
            ], 400);
        }
    }
}

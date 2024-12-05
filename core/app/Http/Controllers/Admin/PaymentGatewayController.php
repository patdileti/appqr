<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Validator;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $gateways = PaymentGateway::all();
        return view('admin.paymentGateways.index', compact('gateways'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\PaymentGateway $PaymentGateway
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentGateway $gateway)
    {
        return view('admin.paymentGateways.edit', compact('gateway'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\PaymentGateway $gateway
     */
    public function update(Request $request, PaymentGateway $gateway)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:100'],
            'active' => ['required', 'boolean'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $gateway->update([
            'payment_install' => $request->active,
            'payment_title' => $request->title,
        ]);

        $options = match ($gateway->payment_folder) {
            'paypal' => [
                'paypal_sandbox_mode',
                'paypal_payment_mode',
                'paypal_api_client_id',
                'paypal_api_secret',
                'paypal_api_app_id',
            ],
            'stripe' => [
                'stripe_payment_mode',
                'stripe_publishable_key',
                'stripe_secret_key',
                'stripe_webhook_secret',
            ],
            'wire_transfer' => [
                'company_bank_info',
            ],
            'two_checkout' => [
                '2checkout_sandbox_mode',
                'checkout_account_number',
                'checkout_public_key',
                'checkout_private_key',
            ],
            'paystack' => [
                'paystack_secret_key',
                'paystack_public_key',
            ],
            'payumoney' => [
                'payumoney_sandbox_mode',
                'payumoney_merchant_pos_id',
                'payumoney_signature_key',
                'payumoney_oauth_client_id',
                'payumoney_oauth_client_secret',
            ],
            'mollie' => [
                'mollie_api_key',
            ],
            'razorpay' => [
                'razorpay_api_key',
                'razorpay_secret_key',
            ],
            'paytm' => [
                'PAYTM_ENVIRONMENT',
                'PAYTM_MERCHANT_KEY',
                'PAYTM_MERCHANT_MID',
                'PAYTM_MERCHANT_WEBSITE',
            ],
            'ccavenue' => [
                'CCAVENUE_MERCHANT_KEY',
                'CCAVENUE_ACCESS_CODE',
                'CCAVENUE_WORKING_KEY',
            ],
            'paytabs' => [
                'paytabs_region',
                'paytabs_profile_id',
                'paytabs_secret_key',
            ],
            'telr' => [
                'telr_sandbox_mode',
                'telr_store_id',
                'telr_authkey',
            ],
            'midtrans' => [
                'midtrans_sandbox_mode',
                'midtrans_client_key',
                'midtrans_server_key',
            ],
            'iyzico' => [
                'iyzico_sandbox_mode',
                'iyzico_api_key',
                'iyzico_secret_key',
            ],
            'paddle' => [
                'paddle_sandbox_mode',
                'paddle_vendor_id',
                'paddle_api_key',
                'paddle_public_key',
            ],
            default => [],
        };

        foreach ($options as $option) {
            Option::updateOptions($option, $request->get($option));
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

}

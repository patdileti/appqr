<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    /**
     * Handle the IPN
     */
    public function ipn(Request $request, $gateway)
    {
        $gateway = PaymentGateway::where('payment_folder', $gateway)->firstOrFail();
        $paymentController = __NAMESPACE__.'\\'
        .str_replace(
            ' ', '',
            ucwords(
                str_replace('_', ' ', $gateway->payment_folder))
        )
        .'Controller';
        return $paymentController::ipn($request);
    }

    /**
     * Handle the Webhook
     */
    public function webhook(Request $request, $gateway)
    {
        $gateway = PaymentGateway::where('payment_folder', $gateway)->firstOrFail();
        $paymentController = __NAMESPACE__.'\\'
            .str_replace(
                ' ', '',
                ucwords(
                    str_replace('_', ' ', $gateway->payment_folder))
            )
            .'Controller';
        return $paymentController::webhook($request);
    }
}

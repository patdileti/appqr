<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Models\Transaction;

class WireTransferController extends Controller
{

    /**
     * Process the payment
     *
     * @param Transaction $transaction
     */
    public static function pay(Transaction $transaction)
    {
        /* Update transaction status */
        $transaction->update(['transaction_gatway' => 'wire_transfer','status' => Transaction::STATUS_PENDING]);

        create_notification(___('New offline payment request is pending.'), 'new_payment', route('admin.transactions.index'));

        /* display wire transfer details */
        return view(active_theme()."user.gateways.wire-transfer", compact('transaction'));
    }
}

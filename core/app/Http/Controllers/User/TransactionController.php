<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tax;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Str;

class TransactionController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $transactions = Transaction::where('user_id', request()->user()->id)->whereIn('status', [Transaction::STATUS_SUCCESS, Transaction::STATUS_PENDING])->orderbyDesc('id')->paginate(30);
        return view($this->activeTheme.'.user.transactions', ['transactions' => $transactions]);
    }

    /**
     * Display the invoice
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function invoice(Transaction $transaction)
    {
        abort_if($transaction->status != Transaction::STATUS_SUCCESS || $transaction->amount == 0, 404);

        if(request()->user()->user_type != 'admin' && $transaction->user_id != request()->user()->id){
            abort(404);
        }

        $title = $transaction->transaction_description;
        $plan = Plan::find($transaction->product_id);
        if($plan) {
            $title = !empty($plan->translations->{get_lang()}->name)
                ? $plan->translations->{get_lang()}->name
                : $plan->name;
            $title .= ' (' . plan_interval_text($transaction->frequency) . ')';
        }

        $item_price = $transaction['base_amount'] ?? $transaction['amount'];
        $applied_taxes = [];
        /* calculate tax */
        if(!empty($transaction->taxes_ids)){
            $taxes = Tax::whereIn('id', explode(',', $transaction->taxes_ids))->get();

            $inclusive_tax = $exclusive_tax = 0;

            foreach ($taxes as $key => $tax){
                $applied_taxes[$key] = $tax;

                /* calculate inclusive taxes */
                if($tax['type'] == 'inclusive'){
                    $value = $tax['value_type'] == 'percentage' ? $item_price * ($tax['value'] / 100) : $tax['value'];
                    $inclusive_tax += $value;
                    $applied_taxes[$key]['value'] = $value;
                }
            }

            $price_without_inclusive = $item_price - $inclusive_tax;

            /* calculate exclusive taxes */
            foreach ($taxes as $key => $tax){
                if($tax['type'] == 'exclusive'){
                    $value = $tax['value_type'] == 'percentage' ? $price_without_inclusive * ($tax['value'] / 100) : $tax['value'];;
                    $exclusive_tax += $value;
                    $applied_taxes[$key]['value'] = $value;
                }
            }
        }

        return view('admin.transactions.invoice', compact('transaction','title',  'applied_taxes'));
    }
}

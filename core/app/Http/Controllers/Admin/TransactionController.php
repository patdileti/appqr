<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Models\Plan;
use App\Models\Tax;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'id',
                'product_name',
                'product_id',
                'user_id',
                'amount',
                'transaction_gatway',
                'status',
                'created_at'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $transaction = Transaction::with(['user'])
                    ->whereIn('status', [Transaction::STATUS_SUCCESS, Transaction::STATUS_PENDING])
                    ->where('id', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $transaction = Transaction::with(['user'])
                    ->whereIn('status', [Transaction::STATUS_SUCCESS, Transaction::STATUS_PENDING])
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Transaction::whereIn('status', [Transaction::STATUS_SUCCESS, Transaction::STATUS_PENDING])->count();
            foreach ($transaction as $row) {

                /* handle deleted users */
                if(!$row->user){
                    continue;
                }

                if ($row->transaction_gatway) {
                    $gateway_badge = '<span class="badge bg-secondary">' . $row->transaction_gatway . '</span>';
                } else {
                    $gateway_badge = '<span>-</span>';
                }

                if ($row->status == "pending") {
                    $status_badge = '<span class="badge bg-info">' . ___('Pending') . '</span>';
                } elseif ($row->status == "success") {
                    $status_badge = '<span class="badge bg-success">' . ___('Paid') . '</span>';
                } elseif ($row->status == "cancel") {
                    $status_badge = '<span class="badge bg-warning">' . ___('Cancelled') . '</span>';
                }

                $invoice_button = '';
                if ($row->status == "success" && $row->amount > 0) {
                    $invoice_button = '<a href="' . route('invoice', $row->id) . '" title="' . ___('Invoice') . '" class="btn btn-default btn-icon" data-tippy-placement="top" target="_blank"><i class="icon-feather-paperclip"></i></a>';
                }

                $approve_button = '';
                if ($row->status == "pending") {
                    $confirm_text = 'onsubmit="return confirm(\''.e(___('Are you sure?')).'\')"';
                    $approve_button = '<form class="d-inline" method = "POST" '.$confirm_text.'
                    action = "'.route('admin.transactions.update', $row->id).'">
                                    '.method_field("put").'
                                    '.csrf_field().'
                                <button class="btn btn-icon btn-primary" title="'.___('Mark as Paid').'" data-tippy-placement="top"><i class="icon-feather-check"></i ></button>
                            </form>';
                }

                $title = $row->transaction_description;
                $plan = Plan::find($row->product_id);
                if($plan){
                    $title = !empty($plan->translations->{get_lang()}->name)
                        ? $plan->translations->{get_lang()}->name
                        : $plan->name;
                    $title .= ' ('. plan_interval_text($row->frequency) .')';
                }

                $rows = array();
                $rows[] = '<td>' . $row->id . '</td>';
                $rows[] = '<td>' . $title . '</td>';
                $rows[] = '<td><a class="text-body" href="'. route('admin.users.edit', $row->user->id).'">' . $row->user->name . '</a></td>';
                $rows[] = '<td>' . price_symbol_format($row->amount) . '</td>';
                $rows[] = '<td>' . $gateway_badge . '</td>';
                $rows[] = '<td>' . $status_badge . '</td>';
                $rows[] = '<td>' . date_formating($row->created_at) . '</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    '.$approve_button.'
                                    <button data-url=" ' . route('admin.transactions.show', $row->id) . '" data-toggle="slidePanel" title="' . ___('Details') . '" class="btn btn-default btn-icon mx-1" data-tippy-placement="top"><i class="icon-feather-list"></i></button>
                                    '.$invoice_button.'
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                <input type="checkbox" id="check_' . $row->id . '" value="' . $row->id . '" class="quick-check">
                                <label for="check_' . $row->id . '"><span class="checkbox-icon"></span></label>
                            </div>
                           </td>';
                $rows['DT_RowId'] = $row->id;
                $data[] = $rows;
            }

            $json_data = array(
                "draw" => intval($params['draw']),
                "recordsTotal" => intval($totalRecords),
                "recordsFiltered" => intval($totalRecords),
                "data" => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.transactions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Transaction $transaction
     */
    public function show(Transaction $transaction)
    {
        abort_if(!in_array($transaction->status, [Transaction::STATUS_SUCCESS, Transaction::STATUS_PENDING]), 404);

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

        return view('admin.transactions.details', compact('transaction','title',  'applied_taxes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Transaction $transaction
     */
    public function update(Request $request, Transaction $transaction)
    {
        if($transaction->status == Transaction::STATUS_PENDING){
            /* Update status and update user's plan for offline payment */
            $transaction->update(['status' => "success"]);
            CheckoutController::updateUserPlan($transaction);
        }
        quick_alert_success(___('Updated Successfully'));
        return back();
    }

    /**
     * Remove multiple resources from storage.
     *
     * @param \App\Models\Transaction $transaction
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $sql = Transaction::whereIn('id', $ids)->delete();
        if ($sql) {
            $result = array('success' => true, 'message' => ___('Deleted Successfully'));
            return response()->json($result, 200);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Validator;

class TaxController extends Controller
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
                'internal_name',
                'name',
                'value',
                'billing_type',
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $tax = Tax::where('internal_name', 'like', '%' . $q . '%')
                    ->OrWhere('name', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $tax = Tax::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Tax::count();
            foreach ($tax as $row) {

                $countries = [];
                if($row->countries == null){
                    $country_name = ___('All Countries');
                }else{
                    $countries = explode(",", $row->countries);
                    foreach ($countries as $country){
                        $countries2[] = '<span class="badge bg-info me-1">'.$country.'</span>';
                    }
                    $country_name = implode(' ', $countries2);
                }

                $rows = array();
                $rows[] = '<td><p>' . $row->internal_name . '</p></td>';
                $rows[] = '<td><p>' . $row->name . '<br>' . $row->description . '</p></td>';
                $rows[] = '<td><p>' . ($row->value_type == 'percentage' ? (float) $row->value .'%' : price_format($row->value)) . '</p></td>';
                $rows[] = '<td><p>' . ($row->type == 'inclusive' ? 'Inclusive' : 'Exclusive') . '</p></td>';
                $rows[] = '<td><div class="d-flex align-items-center">' . $country_name . '</div></td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <button data-url=" ' . route('admin.taxes.edit', $row->id) . '" data-toggle="slidePanel" title="' . ___('Edit') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></button>
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
                "data" => $data // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.taxes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.taxes.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'internal_name' => ['required', 'max:255'],
            'name' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
            'value_type' => ['required'],
            'type' => ['required'],
            'billing_type' => ['required'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $countries = null;
        if ($request->has('countries')) {
            $countries = implode(',',$request->countries);
        }

        $create = Tax::create([
            'internal_name' => $request->internal_name,
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'type' => $request->type,
            'billing_type' => $request->billing_type,
            'countries' => $countries,
        ]);

        if ($create) {
            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     */
    public function show(Tax $tax)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Tax $tax)
    {
        $validator = Validator::make($request->all(), [
            'internal_name' => ['required', 'max:255'],
            'name' => ['required', 'max:255'],
            'description' => ['required', 'max:255'],
            'value' => ['required', 'numeric', 'min:0', 'max:100'],
            'value_type' => ['required'],
            'type' => ['required'],
            'billing_type' => ['required'],
        ]);

        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $countries = null;
        if ($request->has('countries')) {
            $countries = implode(',',$request->countries);
        }

        $update = $tax->update([
            'internal_name' => $request->internal_name,
            'name' => $request->name,
            'description' => $request->description,
            'value' => $request->value,
            'value_type' => $request->value_type,
            'type' => $request->type,
            'billing_type' => $request->billing_type,
            'countries' => $countries,
        ]);

        if ($update) {
            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tax  $tax
     */
    public function destroy(Tax $tax)
    {
        abort(404);
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        Tax::whereIn('id', $ids)->delete();
        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }
}

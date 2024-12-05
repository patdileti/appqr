<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allergy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class AllergyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $params = $columns = $order = $totalRecords = $data = array();
            $params = $request;

            //define index of column
            $columns = array(
                'position',
                'title',
                'active',
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $allergies = Allergy::where('title', 'like', '%'.$q.'%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $allergies = Allergy::orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Allergy::count();
            foreach ($allergies as $row) {

                if ($row->active == 1) {
                    $status_badge = '<span class="badge bg-success">'.___('Enabled').'</span>';
                } else {
                    $status_badge = '<span class="badge bg-danger">'.___('Disabled').'</span>';
                }

                if (Str::isUrl($row->image)) {
                    $image = $row->image;
                } else {
                    $image = asset('storage/allergies/'.$row->image);
                }

                $rows = array();
                $rows[] = '<td><i class="icon-feather-menu quick-reorder-icon"
                                       title="'.___('Reorder').'"></i> <span class="d-none">'.$row->id.'</span></td>';
                $rows[] = '<td><img src="'.$image.'" width="25" class="me-2">'.$row->title.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <button data-url=" '.route('admin.allergies.edit',
                        $row->id).'" data-toggle="slidePanel" title="'.___('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></button>
                                </div>
                           </td>';
                $rows[] = '<td>
                                <div class="checkbox">
                                    <input type="checkbox" id="check_'.$row->id.'" value="'.$row->id.'" class="quick-check">
                                    <label for="check_'.$row->id.'"><span class="checkbox-icon"></span></label>
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

        return view('admin.allergies.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.allergies.create');
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
            'title' => ['required', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
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

        if ($request->has('image') && !empty($request->image)) {
            $image = image_upload($request->file('image'), 'storage/allergies/', '32x32');
        } else {
            $image = 'default.png';
        }

        $create = Allergy::create([
            'title' => $request->title,
            'image' => $image,
            'active' => $request->active,
            'translations' => $request->translations,
            'position' => 999
        ]);

        if ($create) {
            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Allergy  $allergy
     */
    public function show(Allergy $allergy)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Allergy  $allergy
     */
    public function edit(Allergy $allergy)
    {
        return view('admin.allergies.edit', compact('allergy'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Allergy  $allergy
     */
    public function update(Request $request, Allergy $allergy)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg'],
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

        if ($request->has('image') && $request->image != null) {
            if ($allergy->image == 'default.png') {
                $image = image_upload($request->file('image'), 'storage/allergies/', '32x32');
            } else {
                $image = image_upload($request->file('image'), 'storage/allergies/', '32x32', null, $allergy->image);
            }
        } else {
            $image = $allergy->image;
        }

        $update = $allergy->update([
            'title' => $request->title,
            'image' => $image,
            'active' => $request->active,
            'translations' => $request->translations,
        ]);

        if ($update) {
            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Allergy  $allergy
     */
    public function destroy(Allergy $allergy)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PlanOption $planoption
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request)
    {
        $position = $request->position;
        if (is_array($request->position)) {
            $count = 0;
            foreach($position as $id){
                $update = Allergy::where('id',$id)->update([
                    'position' => $count,
                ]);

                $count++;
            }
            if ($update) {
                $result = array('success' => true, 'message' => ___('Updated Successfully'));
                return response()->json($result, 200);
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Remove the multiple resources from storage.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $admins = Allergy::whereIn('id', $ids)->get();
        foreach ($admins as $admin) {
            if ($admin->image != "default.png") {
                remove_file('storage/allergies/'.$admin->image);
            }
        }
        Allergy::whereIn('id', $ids)->delete();

        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Validator;

class BlogCategoryController extends Controller
{

    /**
     * Constructor
     */
    public function __construct()
    {
        abort_if(!config('settings.blog_enable'), 404);
    }

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
                'created_at'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $categories = BlogCategory::where('title', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $categories = BlogCategory::orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = BlogCategory::count();
            foreach ($categories as $row) {

                if ($row->active){
                    $status_badge = '<span class="badge bg-success">'.___('Active').'</span>';
                }else{
                    $status_badge = '<span class="badge bg-danger">'.___('Disabled').'</span>';
                }

                $rows = array();
                $rows[] = '<td><i class="icon-feather-menu quick-reorder-icon" title="' . ___('Reorder') . '"></i> <span class="d-none">' . $row->id . '</span></td>';
                $rows[] = '<td>'.$row->title.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('blog.category', $row->slug).'" target="_blank" title="'.___('View').'" class="btn btn-default btn-icon me-1" data-tippy-placement="top"><i class="icon-feather-eye"></i></a>
                                    <a href="#" data-url="'.route('admin.blog.category.edit', $row->id).'" data-toggle="slidePanel" title="'.___('Edit').'" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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
                "draw"            => intval( $params['draw'] ),
                "recordsTotal"    => intval( $totalRecords ),
                "recordsFiltered" => intval($totalRecords),
                "data"            => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.blog.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.blog.categories.create');
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
            'title' => ['required', 'min:2', 'max:255'],
            'slug' => ['nullable', 'unique:blog_categories', 'alpha_dash'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $category = BlogCategory::create([
            'title' => $request->title,
            'slug' => !empty($request->slug)
                ? $request->slug
                : SlugService::createSlug(BlogCategory::class, 'slug', $request->title),
            'active' => $request->active,
        ]);

        if ($category) {
            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BlogCategory  $category
     */
    public function show(BlogCategory $category)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function edit(BlogCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\BlogCategory  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BlogCategory $category)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'min:2', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'unique:blog_categories,slug,' . $category->id],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        $update = $category->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'active' => $request->active,
        ]);
        if ($update) {
            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BlogCategory  $category
     */
    public function destroy(BlogCategory $category)
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
                $update = BlogCategory::where('id',$id)->update([
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
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $blogcategories = BlogCategory::whereIn('id',$ids)->get();
        foreach ($blogcategories as $blogcategory) {
            $blogcategory->blogs()->detach();
            $blogcategory->delete();
        }
        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
        exit();
    }
}

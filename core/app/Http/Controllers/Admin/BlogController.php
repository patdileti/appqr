<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Validator;

class BlogController extends Controller
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
                'title',
                '','','',
                'updated_at'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $blogs = Blog::with(['categories','user'])
                    ->withCount('comments')
                    ->where('id', 'like', '%' . $q . '%')
                    ->OrWhere('title', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $blogs = Blog::with(['categories','user'])
                    ->withCount('comments')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Blog::count();
            foreach ($blogs as $row) {

                $categories = [];
                foreach ($row->categories as $category){
                    $categories[] = '<span class="badge bg-info">'.$category->title.'</span>';
                }
                $categories = !empty($categories) ? implode(' ',$categories) : '&#8211;';

                if ($row->status == "publish"){
                    $status_badge = '<span class="badge bg-success">'.___('Publish').'</span>';
                }else{
                    $status_badge = '<span class="badge bg-warning">'.___('Pending').'</span>';
                }

                $rows = array();
                $rows[] = '<td>' . $row->id . '</td>';
                $rows[] = '<td><a class="text-body" href="' . route('admin.blogs.edit', $row->id) . '">' . text_shorting($row->title, 30) . '</a></td>';
                $rows[] = '<td>' . $categories . '</td>';
                $rows[] = '<td>' . $row->comments_count . '</td>';
                $rows[] = '<td>' . $status_badge . '</td>';
                $rows[] = '<td>' . date_formating($row->updated_at) . '</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="' . route('blog.single', $row->id) . '" title="' . ___('View') . '" data-tippy-placement="top" class="btn btn-default btn-icon me-1" target="_blank"><i class="icon-feather-eye"></i></a>
                                    <a href="' . route('admin.blog.comments.index') . '?blog_id=' . $row->id . '" title="' . ___('Comments') . '" data-tippy-placement="top" class="btn btn-default btn-icon me-1"><i class="icon-feather-message-square"></i></a>
                                    <a href="' . route('admin.blogs.edit', $row->id) . '" title="' . ___('Edit') . '" class="btn btn-default btn-icon" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
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

        return view('admin.blog.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = BlogCategory::where('active', "1")->get();
        return view('admin.blog.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'unique:blog,slug'],
            'description' => ['required'],
            'image' => ['required','mimes:png,jpg,jpeg', 'max:2048'],
            'status' => ['required', 'string'],
            'category' => ['required'],
            'tags' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }

        $image = image_upload($request->file('image'), 'storage/blog/', '1300x740');

        if ($image) {
            $blog = Blog::create([
                'author' => auth()->id(),
                'title' => $request->title,
                'slug' => !empty($request->slug)
                    ? $request->slug
                    : SlugService::createSlug(Blog::class, 'slug', $request->title),
                'description' => $request->description,
                'image' => $image,
                'tags' => Str::lower($request->tags),
                'status' => $request->status,
            ]);
            $blog->categories()->attach($request->category);

            if ($blog) {
                quick_alert_success(___('Created Successfully'));
                return redirect(route('admin.blogs.index'));
            }
        } else {
            quick_alert_error(___('Unable to upload the image, please try again.'));
            return back()->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Blog $blog
     */
    public function show(Blog $blog)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\Response
     */
    public function edit(Blog $blog)
    {
        $categories = BlogCategory::where('active', "1")->get();

        return view('admin.blog.edit', compact('blog', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Blog $blog
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blog $blog)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => ['nullable', 'alpha_dash', 'unique:blog,slug,' . $blog->id],
            'description' => ['required'],
            'image' => ['mimes:png,jpg,jpeg', 'max:2048'],
            'status' => ['required', 'string'],
            'category' => ['required'],
            'tags' => ['nullable', 'string'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }

        if ($request->has('image')) {
            $image = image_upload($request->file('image'), 'storage/blog/', '1300x740', null, $blog->image);
        } else {
            $image = $blog->image;
        }
        if ($image) {
            $update = $blog->update([
                'author' => auth()->id(),
                'title' => $request->title,
                'slug' => !empty($request->slug)
                    ? $request->slug
                    : SlugService::createSlug(Blog::class, 'slug', $request->title),
                'description' => $request->description,
                'image' => $image,
                'tags' => Str::lower($request->tags),
                'status' => $request->status,
            ]);
            $blog->categories()->sync($request->category);
            if ($update) {
                quick_alert_success(___('Updated Successfully'));
                return back();
            }
        } else {
            quick_alert_error(___('Unable to upload the image, please try again.'));
            return back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Blog $blog
     */
    public function destroy(Blog $blog)
    {
        abort(404);
    }

    /**
     *  Remove the multiple resources
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        $blogs = Blog::whereIn('id', $ids)->get();
        foreach ($blogs as $blog) {
            $blog->categories()->detach();
            remove_file('storage/blog/' . $blog->image);
        }
        Blog::whereIn('id', $ids)->delete();
        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }
}

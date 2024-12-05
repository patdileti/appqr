<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Validator;

class PostController extends Controller
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
                'title',
                'created_at'
            );

            if (!empty($params['search']['value'])) {
                $q = $params['search']['value'];
                $posts = Post::with('user')
                    ->where('title', 'like', '%'.$q.'%')
                    ->OrWhere('description', 'like', '%'.$q.'%')
                    ->OrWhere('sub_title', 'like', '%'.$q.'%')
                    ->OrWhere('address', 'like', '%'.$q.'%')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            } else {
                $posts = Post::with('user')
                    ->orderBy($columns[$params['order'][0]['column']], $params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = Post::count();
            foreach ($posts as $row) {

                /* handle deleted users */
                if (!$row->user) {
                    continue;
                }

                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td>
                                <div class="quick-user-box align-items-center">
                                    <a class="quick-user-avatar"
                                        href="'.route('publicView', $row->slug).'" target="_blank">
                                        <img src="'.asset('storage/restaurant/logo/'.$row->main_image).'" />
                                    </a>
                                    <a class="text-reset"
                                            href="'.route('publicView', $row->slug).'" target="_blank">'.$row->title.'</a>
                                </div>
                            </td>';
                $rows[] = '<td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <a href="'.route('admin.users.edit', $row->user->id).'">
                                            <img alt="'.$row->user->username.'" src="'.asset('storage/profile/'.$row->user->image).'" />
                                        </a>
                                    </div>
                                    <div>
                                        <a class="text-body fw-semibold text-truncate"
                                            href="'.route('admin.users.edit', $row->user->id).'">'.$row->user->name.'</a>
                                        <p class="text-muted mb-0">@'.$row->user->username.'</p>
                                    </div>
                                </div>
                            </td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('publicView', $row->slug).'" title="'.___('View').'" class="btn btn-primary btn-icon" data-tippy-placement="top" target="_blank"><i class="icon-feather-eye"></i></a>
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
                "data" => $data   // total data array
            );
            return response()->json($json_data, 200);
        }

        return view('admin.posts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $ids = array_map('intval', $request->ids);
        Post::query()
            ->whereIn('id', $ids)
            ->get()
            ->each(function ($item) {
                $item->delete();
            });

        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }

}

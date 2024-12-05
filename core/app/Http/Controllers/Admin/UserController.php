<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mails\UserDetails;
use App\Models\Country;
use App\Models\Notification;
use App\Models\Upgrade;
use App\Models\User;
use App\Models\UserOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;

class UserController extends Controller
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
                'name',
                'email',
                'email_verified_at',
                'status'
            );

            if(!empty($params['search']['value'])){
                $q = $params['search']['value'];
                $users = User::where('id', 'like', '%' . $q . '%')
                    ->OrWhere('name', 'like', '%' . $q . '%')
                    ->OrWhere('username', 'like', '%' . $q . '%')
                    ->OrWhere('email', 'like', '%' . $q . '%')
                    ->orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }else{
                $users = User::orderBy($columns[$params['order'][0]['column']],$params['order'][0]['dir'])
                    ->limit($params['length'])->offset($params['start'])
                    ->get();
            }

            $totalRecords = User::count();
            foreach ($users as $row) {

                if ($row->email_verified_at)
                    $email_badge = '<span class="badge bg-success">'.___('Verified').'</span>';
                else
                    $email_badge = '<span class="badge bg-warning">'.___('Unverified').'</span>';

                if ($row->status)
                    $status_badge = '<span class="badge bg-success">'.___('Active').'</span>';
                else
                    $status_badge = '<span class="badge bg-danger">'.___('Banned').'</span>';


                $rows = array();
                $rows[] = '<td>'.$row->id.'</td>';
                $rows[] = '<td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar me-2">
                                        <a href="'.route('admin.users.edit', $row->id).'">
                                            <img class="rounded-circle" alt="'.$row->username.'" src="'.asset('storage/profile/'.$row->image).'" />
                                        </a>
                                    </div>
                                    <div>
                                        <a class="text-body fw-semibold text-truncate"
                                            href="'.route('admin.users.edit', $row->id).'">'.$row->name.'</a>
                                        <p class="text-muted mb-0">@'.$row->username.'</p>
                                    </div>
                                </div>
                            </td>';
                $rows[] = '<td><span class="text-truncate">'.$row->email.'</span></td>';
                $rows[] = '<td>'.$email_badge.'</td>';
                $rows[] = '<td>'.$status_badge.'</td>';
                $rows[] = '<td>'.date_formating($row->created_at).'</td>';
                $rows[] = '<td>
                                <div class="d-flex">
                                    <a href="'.route('admin.users.edit', $row->id).'" title="'.___('Edit').'" class="btn btn-icon btn-default me-1" data-tippy-placement="top"><i class="icon-feather-edit"></i></a>
                                    <form action="'.route('admin.users.login', $row->id).'" method="post">
                                    '.csrf_field().'
                                    <button title="'.___('Login as User').'" class="btn btn-icon btn-default" data-tippy-placement="top"><i class="icon-feather-log-in"></i></button>
</form>
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

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
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
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:user'],
            'email' => ['required', 'email', 'string', 'max:100', 'unique:user'],
            'password' => ['required', 'string', 'min:6'],
            'country' => ['required', 'integer', 'exists:countries,id'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = array('success' => false, 'message' => implode('<br>', $errors));
            return response()->json($result, 200);
        }

        if (!empty($request->file('avatar'))) {
            $avatar = image_upload($request->file('avatar'), 'storage/profile/', '150x150');
        } else {
            $avatar = "default_user.png";
        }

        $country = Country::find($request->country);

        $user = User::create([
            'user_type' => 'user',
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'image' => $avatar,
            'group_id' => config('settings.default_user_plan'),
            'country_code' => $country->code,
            'country' => $country->name,
        ]);
        if ($user) {
            $user->markEmailAsVerified();

            /* Send user details email */
            $user->sendMail(new UserDetails($user));

            $result = array('success' => true, 'message' => ___('Created Successfully'));
            return response()->json($result, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     */
    public function show(User $user)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tax  $tax
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'user_type' => ['required', 'string', 'max:5'],
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'name' => ['required', 'string', 'max:50'],
            'username' => ['required', 'string', 'max:50', 'unique:user,username,' . $user->id],
            'email' => ['required', 'email', 'string', 'max:100', 'unique:user,email,' . $user->id],
            'country' => ['required', 'integer', 'exists:countries,id'],
        ]);
        $errors = [];
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }

        $avatar = $user->image;
        if ($request->file('avatar')) {
            if ($user->image == 'default_user.png') {
                $avatar = image_upload($request->file('avatar'), 'storage/profile/', '150x150');
            } else {
                $avatar = image_upload($request->file('avatar'), 'storage/profile/', '150x150', null, $user->image);
            }
        }

        $country = Country::find($request->country);

        $update = $user->update([
            'user_type' => $request->user_type,
            'image' => $avatar,
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'status' => $request->status,
            'country_code' => $country->code,
            'country' => $country->name,
        ]);
        if ($update) {

            $user->forceFill([
                'email_verified_at' => ($request->email_status) ? Carbon::now() : null,
            ])->save();

            quick_alert_success(___('Updated Successfully'));
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     */
    public function destroy(User $user)
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
        $users = User::with('posts')->whereIn('id',$ids)->get();
        foreach($users as $user){
            if ($user->image != "default_user.png") {
                remove_file('storage/profile/'.$user->image);
            }

            if($user->posts){
                foreach ($user->posts as $post){
                    $post->delete();
                }
            }

            /* remove admin notifications of this user */
            $notifications = Notification::where('link', route('admin.users.edit', $user->id))->get();
            foreach ($notifications as $notification) {
                $notification->delete();
            }
        }
        User::whereIn('id',$ids)->delete();

        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Remove user's avatar
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function deleteAvatar(User $user)
    {
        $avatar = "default_user.png";
        if ($user->image != "default_user.png") {
            remove_file('storage/profile/'.$user->image);
        }

        $update = $user->update([
            'image' => $avatar,
        ]);
        if ($update) {
            quick_alert_success(___('Removed Successfully'));
            return back();
        }
    }

    /**
     * Display password form
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function password(User $user)
    {
        return view('admin.users.password', compact('user'));
    }

    /**
     * Update password
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }

        $update = $user->update([
            'password' => Hash::make($request->password),
        ]);
        if ($update) {
            quick_alert_success(___('Password changed successfully'));
            return back();
        }
    }

    /**
     * Send user email
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     */
    public function sendMail(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'subject' => ['required', 'string'],
            'message' => ['required', 'string'],
            'reply_to' => ['required', 'email'],
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }

        try {
            $email = $user->email;
            $subject = $request->subject;
            $replyTo = $request->reply_to;
            $msg = $request->message;

            \Mail::send([], [], function ($message) use ($msg, $email, $subject, $replyTo) {
                $message->to($email)
                    ->replyTo($replyTo)
                    ->subject($subject)
                    ->html($msg);
            });
            quick_alert_success(___('Sent successfully'));
            return back();
        } catch (\Exception $e) {
            quick_alert_error(___('Error in sending email') . ' ' . $e->getMessage());
            return back();
        }
    }

    /**
     * Update user plan
     *
     * @param Request $request
     * @param User $user
     */
    public function updatePlan(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'plan' => ['required']
        ]);
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back();
        }

        /* Delete old membership */
        Upgrade::where('user_id', $user->id)->delete();

        if($request->plan != 'free') {
            /* Create new membership */
            Upgrade::create([
                'sub_id' => $request->plan,
                'user_id' => $user->id,
                'interval' => null,
                'upgrade_lasttime' => Carbon::now()->timestamp,
                'upgrade_expires' => strtotime($request->plan_expiration_date),
                'status' => Upgrade::STATUS_ACTIVE,
            ]);
        }

        /* Update user group */
        $user->update([
            'group_id' => $request->plan,
        ]);

        UserOption::updateUserOption($user->id, 'package_trial_done', $request->package_trial_done);

        quick_alert_success(___('Updated successfully'));
        return back();
    }

    /**
     * Login as user
     *
     * @param User $user
     */
    public function loginAsUser(User $user)
    {
        /* already logged in */
        if($user->id == request()->user()->id) {
            quick_alert_info(___('Already logged in as :user_name.', ['user_name' => '<strong>'.$user->name.'</strong>']));
            return back();
        }

        $admin_id = request()->user()->id;

        /* logout admin user */
        Auth::logout();

        /* login as new user */
        Auth::login($user);

        /* save admin user id in session */
        session()->put('quick_admin_user_id', $admin_id);

        quick_alert_success(___('You are logged in as :user_name.', ['user_name' => $user->name]));

        return redirect(route('dashboard'));
    }
}

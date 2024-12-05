<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mails\UserDetails;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
     */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::USER;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->activeTheme = active_theme();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        return view($this->activeTheme . 'auth.register');
    }

    /**
     * Before register a new user
     *
     * @return //redirect
     */
    public function register(Request $request)
    {
        Validator::make($request->all(), [
                'name' => ['sometimes', 'string', 'min:2', 'max:40'],
                'username' => ['required', 'alpha_dash', 'min:2', 'max:16', 'unique:user'],
                'email' => ['required', 'string', 'email', 'max:100', 'unique:user'],
                'password' => ['required', 'string', 'min:6', 'max:20'],
                'agree_for_term' => ['sometimes', 'required'],
            ] + validate_recaptcha())
            ->validate();

        $ipInfo = user_ip_info();

        $data = array_merge($request->all(), [
            'country_name' => $ipInfo->location->country,
            'country_code' => $ipInfo->location->country_code,
            'city' => $ipInfo->location->city,
        ]);

        $user = User::create([
            'name' => $data['name'] ?? $data['username'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'group_id' => config('settings.default_user_plan'),
            'country' => $data['country_name'],
            'country_code' => $data['country_code'],
        ]);
        if ($user) {
            /* Send user details email */
            $user->sendMail(new UserDetails($user));
            $user->sendEmailVerificationNotification();

            /* Add admin notification */
            $title = ___(':user_name has registered', ['user_name' => $user->name]);
            create_notification($title, 'new_user', route('admin.users.edit', $user->id));
        }

        //event(new Registered($user));

        $this->guard()->login($user);

        return $this->registered($request, $user) ?: redirect($this->redirectPath());
    }

    /**
     * Validate signup form via ajax
     *
     * @param Request $request
     */
    public function checkAvailability(Request $request)
    {
        if($request->has('name')){
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'min:2', 'max:40'],
            ]);
        }

        if($request->has('username')){
            $validator = Validator::make($request->all(), [
                'username' => ['required', 'alpha_dash', 'min:2', 'max:16', 'unique:user'],
            ]);
        }

        if($request->has('email')){
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'string', 'email', 'max:100', 'unique:user'],
            ]);
        }

        if($request->has('password')){
            $validator = Validator::make($request->all(), [
                'password' => ['required', 'string', 'min:6'],
            ]);
        }

        if($validator->fails()){
            $error = $validator->errors()->first();
            echo "<span class='status-not-available'> ".$error."</span>";
        } else {
            echo "<span class='status-available'>".__('Success')."</span>";
        }
    }

}

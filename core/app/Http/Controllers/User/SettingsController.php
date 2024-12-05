<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\UserOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;

class SettingsController extends Controller
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
        return view($this->activeTheme.'.user.settings', ['user' => request()->user(), 'user_options' => user_options(request()->user()->id)]);
    }

    /**
     * Edit user details
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function editProfile(Request $request)
    {
        Validator::make($request->all(), [
            'avatar' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'username' => ['required', 'alpha_dash', 'min:2', 'max:16', 'unique:user,username,' . request()->user()->id],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:user,email,' . request()->user()->id],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ])->validate();

        if ($request->has('avatar')) {
            if (request()->user()->image == 'default_user.png') {
                $image = image_upload($request->file('avatar'), 'storage/profile/', '150x150');
            } else {
                $image = image_upload($request->file('avatar'), 'storage/profile/', '150x150', null, request()->user()->image);
            }
        } else {
            $image = request()->user()->image;
        }

        $verify = request()->user()->email != $request->email;

        request()->user()->username = $request->username;
        request()->user()->email = $request->email;
        request()->user()->image = $image;

        if($request->new_password) {
            $request->user()->password = Hash::make($request->new_password);

            Auth::logoutOtherDevices($request->new_password);
        }
        $update = $request->user()->save();
        if ($update) {
            if ($verify) {
                // unverified email address
                request()->user()->forceFill(['email_verified_at' => null])->save();
                request()->user()->sendEmailVerificationNotification();
            }

            quick_alert_success(___('User details updated successfully'));
            return back();
        }
    }

    /**
     * Edit billing details
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function editBilling(Request $request)
    {
        $request->validateWithBag('billing', [
            'billing_details_type' => ['required'],
            'billing_name' => ['required'],
            'billing_address' => ['required'],
            'billing_city' => ['required'],
            'billing_state' => ['required'],
            'billing_zipcode' => ['required'],
            'billing_country' => ['required', 'string', 'exists:countries,code'],
        ]);

        $requestData = $request->except('submit', '_token');
        foreach ($requestData as $key => $value) {
            UserOption::updateUserOption($request->user()->id,$key, $value);
        }
        $country = Country::where('code', $request->billing_country)->first();
        $request->user()->update([
            'country_code' => $request->billing_country,
            'country' => $country->name,
        ]);

        quick_alert_success(___('Updated Successfully'));
        return back();
    }
}

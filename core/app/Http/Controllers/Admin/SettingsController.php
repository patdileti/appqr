<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Validator;

class SettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        /**
         * General Setting
         **/
        if ($request->has('general_setting')) {
            $validator = Validator::make($request->all(), [
                'site_title' => 'required|string|max:255',
                'try_demo_link' => 'nullable|url',
                'termcondition_link' => 'nullable|url',
                'privacy_link' => 'nullable|url',
                'cookie_link' => 'nullable|url',
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $requestData = $request->except('general_setting');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            set_env('APP_DEBUG', $request->quickad_debug);


            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /**
         * General Setting
         **/
        if ($request->has('theme_setting')) {
            $validator = Validator::make($request->all(), [
                'theme_color' => 'required|regex:/^#[A-Fa-f0-9]{6}$/',
                'contact_email' => 'nullable|email',
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $requestData = $request->except('theme_setting');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }


            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /**
         * Site Logo & Favicon Settings
         **/
        if ($request->has('logo_setting')) {
            $validator = Validator::make($request->all(), [
                'site_favicon' => 'nullable|mimes:png,jpg,jpeg',
                'site_logo' => 'nullable|mimes:png,jpg,jpeg',
                'site_logo_footer' => 'nullable|mimes:png,jpg,jpeg',
                'site_admin_logo' => 'nullable|mimes:png,jpg,jpeg',
                'social_share_image' => 'nullable|mimes:jpg,jpeg',
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $requestData = $request->except('logo_setting');

            if ($request->has('site_favicon') && $request->file('site_favicon') != null) {
                $site_favicon = image_upload($request->file('site_favicon'), 'storage/logo/',null, null, config('settings.site_favicon'));
                $requestData['site_favicon'] = $site_favicon;
            } else {
                $requestData['site_favicon'] = config('settings.site_favicon');
            }

            if ($request->has('site_logo') && $request->file('site_logo') != null) {
                $site_logo = image_upload($request->file('site_logo'), 'storage/logo/', null,null, config('settings.site_logo'));
                $requestData['site_logo'] = $site_logo;
            } else {
                $requestData['site_logo'] = config('settings.site_logo');
            }

            if ($request->has('site_logo_footer') && $request->file('site_logo_footer') != null) {
                $site_logo_footer = image_upload($request->file('site_logo_footer'), 'storage/logo/',null, null, config('settings.site_logo_footer'));
                $requestData['site_logo_footer'] = $site_logo_footer;
            } else {
                $requestData['site_logo_footer'] = config('settings.site_logo_footer');
            }

            if ($request->has('site_admin_logo') && $request->file('site_admin_logo') != null) {
                $site_admin_logo = image_upload($request->file('site_admin_logo'), 'storage/logo/',null, null, config('settings.site_admin_logo'));
                $requestData['site_admin_logo'] = $site_admin_logo;
            } else {
                $requestData['site_admin_logo'] = config('settings.site_admin_logo');
            }

            if ($request->has('social_share_image') && $request->file('social_share_image') != null) {
                $social_share_image = image_upload($request->file('social_share_image'), 'storage/logo/', '600x315', null, config('settings.social_share_image'));
                $requestData['social_share_image'] = $social_share_image;
            } else {
                $requestData['social_share_image'] = config('settings.social_share_image');
            }

            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /**
         * International Setting
         **/
        if ($request->has('international_setting')) {
            $validator = Validator::make($request->all(), [
                'currency_code' => ['required', 'string', 'max:4', 'regex:/^[A-Z]{3}$/'],
                'currency_sign' => ['required', 'string', 'max:4'],
                'currency_pos' => ['required', 'integer', 'min:0', 'max:1'],
                'timezone' => 'required|in:' . implode(',', array_keys(config('timezones')))
            ]);
            $errors = [];
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $requestData = $request->except('international_setting');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            set_env('DEFAULT_LANGUAGE', $requestData['lang']);
            set_env('APP_TIMEZONE', $requestData['timezone'], true);

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * SMTP Settings
         **/
        if ($request->has('smtp_settings')) {

            $validator = Validator::make($request->all(), [
                'admin_email' => ['required', 'email'],
                'smtp_from_email' => ['required', 'email'],
                'smtp_from_name' => ['required'],
                'smtp_mailer' => ['required', 'in:smtp,sendmail,log'],
                'smtp_secure' => ['required', 'in:ssl,tls']
            ]);

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $requestData = $request->except('smtp_settings');
            foreach ($requestData as $key => $value) {
                $update = Option::updateOptions($key, $value);
            }

            if ($update) {
                set_env('MAIL_MAILER', $requestData['smtp_mailer']);
                set_env('MAIL_HOST', $requestData['smtp_host']);
                set_env('MAIL_PORT', $requestData['smtp_port']);
                set_env('MAIL_USERNAME', $requestData['smtp_username']);
                set_env('MAIL_PASSWORD', $requestData['smtp_password']);
                set_env('MAIL_ENCRYPTION', $requestData['smtp_secure']);
                set_env('MAIL_FROM_ADDRESS', $requestData['smtp_from_email']);
                set_env('MAIL_FROM_NAME', $requestData['smtp_from_name'], true);

                $result = array('success' => true, 'message' => ___('Updated Successfully'));
                return response()->json($result, 200);
            } else {
                $result = array('success' => false, 'message' => ___('Error in updating, please try again.'));
                return response()->json($result, 200);
            }
        }

        /*
         * SMTP Testing
         **/
        if ($request->has('smtp_test')) {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->all() as $error) {$errors[] = $error;}
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            try {
                $email = $request->email;
                \Mail::raw('Hi, This is a test mail to ' . $email, function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Test mail to ' . $email);
                });
                $result = array('success' => true, 'message' => ___('Sent Successfully'));
                return response()->json($result, 200);

            } catch (\Exception $e) {
                $result = array('success' => false, 'message' => ___('Error in sending, please try again.') .' '. $e->getMessage());
                return response()->json($result, 200);
            }
        }

        /*
         * Invoice Billing Settings
         **/
        if ($request->has('billing_settings')) {

            //Option::updateOptions('invoice_billing', $request->invoice_billing);

            $requestData = $request->except('billing_settings');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Social Logins Settings
         **/
        if ($request->has('social_logins_settings')) {

            $requestData = $request->except('social_logins_settings');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            set_env('FACEBOOK_CLIENT_ID', $requestData['facebook_app_id']);
            set_env('FACEBOOK_CLIENT_SECRET', $requestData['facebook_app_secret']);

            set_env('GOOGLE_CLIENT_ID', $requestData['google_app_id']);
            set_env('GOOGLE_CLIENT_SECRET', $requestData['google_app_secret']);

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Blog Settings
         **/
        if ($request->has('blog_settings')) {

            $requestData = $request->except('blog_settings');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Testimonial Settings
         **/
        if ($request->has('testimonial_settings')) {

            $requestData = $request->except('testimonial_settings');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Custom Code Settings
         **/
        if ($request->has('custom_code_setting')) {

            $requestData = $request->except('custom_code_setting');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Addons Settings
         **/
        if ($request->has('addons_settings')) {

            $requestData = $request->except('addons_settings');
            foreach ($requestData as $key => $value) {
                Option::updateOptions($key, $value);
            }

            set_env('NOCAPTCHA_SITEKEY', $requestData['recaptcha_public_key']);
            set_env('NOCAPTCHA_SECRET', $requestData['recaptcha_private_key']);

            $result = array('success' => true, 'message' => ___('Updated Successfully'));
            return response()->json($result, 200);
        }

        /*
         * Clear all cache
         **/
        if ($request->has('clear_cache')) {
            Artisan::call('optimize:clear');
            remove_file(storage_path('logs/laravel.log'));
            $result = array('success' => true, 'message' => ___('Cache Cleared Successfully'));
            return response()->json($result, 200);
        }

    }
}

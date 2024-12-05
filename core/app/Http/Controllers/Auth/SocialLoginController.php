<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mails\UserDetails;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect to social provider
     */
    public function redirect($provider)
    {
        abort_if(!@config('settings.'.$provider.'_login'), 404);
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle the callback
     */
    public function callback($provider)
    {
        try {

            abort_if(!@config('settings.'.$provider.'_login'), 404);

            $user = Socialite::driver($provider)->user();

            $existing_user = User::where('oauth_uid', $user->getId())
                ->orWhere('email', $user->getEmail())->first();

            if ($existing_user) {
                Auth::login($existing_user);

                return redirect(RouteServiceProvider::USER);

            } else {
                if (!config('settings.enable_user_registration', 1)) {
                    quick_alert_error(___('Registration is currently disabled.'));
                    return redirect()->route('login');
                }

                $ipInfo = user_ip_info();

                $username = explode('@', $user->getEmail());
                $username = SlugService::createSlug(User::class, 'username', $username[0]);
                $username = str_replace('-','_', $username);

                $new_user = User::create([
                    'name' => $user->getName(),
                    'username' => $username,
                    'email' => $user->getEmail(),
                    'password' => Hash::make(Str::random(10)),
                    'group_id' => config('settings.default_user_plan'),
                    'country' => $ipInfo->location->country,
                    'country_code' => $ipInfo->location->country_code,
                    'oauth_uid'=> $user->getId(),
                    'oauth_provider'=> $provider,
                ]);
                $new_user->markEmailAsVerified();

                /* Send user details email */
                $new_user->sendMail(new UserDetails($new_user));

                //event(new Registered($new_user));

                $title = ___(':user_name has registered', ['user_name' => $new_user->name]);
                $link = route('admin.users.edit', $new_user->id);
                create_notification($title, 'new_user', $link);

                Auth::login($new_user);

                return redirect(RouteServiceProvider::USER);
            }

        } catch (Exception $e) {
            quick_alert_error($e->getMessage());
            return redirect()->route('login');
        }
    }

}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRegistrationDisabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!config('settings.enable_user_registration', 1)) {
            quick_alert_error(___('Registration is currently disabled.'));
            return redirect()->route('login');
        }
        return $next($request);
    }
}

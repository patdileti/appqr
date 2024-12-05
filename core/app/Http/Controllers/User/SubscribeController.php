<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class SubscribeController extends Controller
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
        $plan = request()->user()->plan();
        $start_date = $expiry_date = '-';
        $pay_mode = 'one_time';
        $interval = null;

        if ($upgrade = request()->user()->upgrade) {
            $start_date = date_formating($upgrade->upgrade_lasttime);
            $expiry_date = date_formating($upgrade->upgrade_expires);
            $pay_mode = $upgrade->pay_mode;
            $interval = $upgrade->interval;
        }

        return view($this->activeTheme.'.user.subscription',
            compact('plan', 'start_date', 'expiry_date', 'pay_mode', 'interval'));
    }

    /**
     * Cancel recurring subscription
     *
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function cancelSubscription()
    {

        try {
            request()->user()->cancelRecurringSubscription();
        } catch (\Exception $e) {

            Log::info($e->getMessage());
            quick_alert_error($e->getMessage());
            return back();
        }

        quick_alert_success(___('Cancelled Successfully'));
        return back();
    }
}

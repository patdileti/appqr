<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PostView;
use Carbon\Carbon;

class DashboardController extends Controller
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
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();

        $days = $scans = [];
        $startDate = $startDate->copy();
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $days[] = date_formating($date, 'd M');
            $scans[date_formating($date, 'd M')] = 0;
        }

        $post_ids = [];
        foreach ( request()->user()->posts as $post ) {
            $post_ids[] = $post->id;
        }

        if (!empty($post_ids)) {
            $post_scans = PostView::where('date', '>=', Carbon::now()->startOfMonth())
                ->selectRaw('DATE(date) as created, COUNT(1) as scans')
                ->whereIn('post_id', $post_ids)
                ->groupBy('created')
                ->get();

            foreach ($post_scans as $data) {
                $scans[date_formating($data->created, 'd M')] = $data->scans;
            }
        }
        $scans = array_values($scans);

        return view($this->activeTheme.'.user.dashboard', compact('days', 'scans'));
    }
}

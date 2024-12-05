<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UpdateController extends Controller
{
    /**
     * Display the update page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('update.index');
    }

    /**
     * Run the update
     */
    public function run()
    {

        $last_updated_version = env('APP_VERSION', '1.0');

        if(version_compare(config('appinfo.version'), $last_updated_version, '>')) {

            /* Enable maintenance mode */
            Artisan::call('down');

            /* Clear cache */
            Artisan::call('optimize:clear');

            /* Get upgrade files */
            $files = array_filter(glob(database_path('upgrades/*.php')), 'is_file');

            $filesArray = [];
            foreach ($files as $file) {
                $v = basename($file, '.php');
                $filesArray[$v] = $file;
            }

            /* Sort versions */
            $versions = array_keys($filesArray);
            usort($versions, 'version_compare');

            /* Run the files */
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                foreach ($versions as $version) {
                    if (version_compare($version, $last_updated_version, '>')) {
                        if (File::exists($filesArray[$version])) {
                            require_once $filesArray[$version];
                        }
                    }
                }
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            } catch (\Exception $e) {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                /* Disable maintenance mode */
                Artisan::call('up');

                return back()->with('error', $e->getMessage());
            }

            /* Update last updated version */
            set_env('APP_VERSION', config('appinfo.version'));

            /* Clear cache */
            Artisan::call('optimize:clear');

            /* Disable maintenance mode */
            Artisan::call('up');
        } else {
            toastr('Already using the latest version.', 'info');
        }

        return redirect()->route('update.complete');
    }

    /**
     * Update completed
     */
    public function complete()
    {
        return view('update.complete');
    }

}

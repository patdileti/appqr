<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Request $request)
    {
        if (env('APP_INSTALLED')) {
            try {
                $settings = Option::all()->pluck('option_value', 'option_name');

                // Update the app's name
                config(['app.name' => $settings['site_title']]);

                // Save all the settings in a config array
                foreach ($settings as $key => $value) {
                    $value = Str::isJson($value) ? json_decode($value) : $value;
                    config(['settings.' . $key => $value]);
                }
            } catch (\Exception $e) {
            }
        }
    }
}

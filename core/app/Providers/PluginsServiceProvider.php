<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class PluginsServiceProvider extends ServiceProvider
{

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        /* Load the plugins Services Provider & register them */
        $plugins = get_installed_plugins();
        foreach ($plugins as $plugin) {
            if ($plugin->enabled) {
                $pluginName = $plugin->id;
                $pluginDir = base_path('plugins/'.$pluginName);

                /* Load plugin views */
                $this->loadViewsFrom(realpath($pluginDir.'/resources/views'), $pluginName);

                /* Register plugin routes */
                if (File::exists($pluginDir.'/routes/web.php')) {
                    Route::middleware('web')
                        ->namespace("Plugins\\$pluginName\app\Http\Controllers") // Assigning namespace
                        ->group(function () use ($pluginDir) {
                            $this->loadRoutesFrom(realpath($pluginDir.'/routes/web.php'));
                        });
                }
            }
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     * @throws \Exception
     */
    public function register(): void
    {

    }
}

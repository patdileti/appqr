<?php

/**
 * Get all installed plugins
 *
 * @return array
 */
function get_installed_plugins()
{
    $plugins = [];
    $pluginsDirs = glob(base_path('plugins/').'*', GLOB_ONLYDIR);
    if (!empty($pluginsDirs)) {
        foreach ($pluginsDirs as $pluginDir) {
            $pluginName = basename($pluginDir);
            if ($plugin = get_plugin($pluginName)) {
                $plugins[$pluginName] = $plugin;
            }
        }
    }

    return $plugins;
}

/**
 * Get plugin details
 *
 * @param  string  $key
 * @return mixed
 */
function get_plugin($key)
{
    if (file_exists(base_path('plugins/'.$key.'/plugin.php'))) {
        $plugin = array_to_object(require base_path('plugins/'.$key.'/plugin.php'));

        $plugin->enabled = is_plugin_enabled($plugin->id);

        return $plugin;
    } else {
        return false;
    }
}

/**
 * Check plugin is enabled
 *
 * @param $plugin_id
 * @return bool
 */
function is_plugin_enabled($plugin_id)
{
    if (file_exists(base_path('plugins/'.$plugin_id.'/plugin.php'))) {
        $active_plugins = config('settings.active_plugins', []);
        return in_array($plugin_id, $active_plugins);
    }
    return false;
}

/**
 * Get plugin assets url
 *
 * @param $plugin_id
 * @param $file
 * @return string|void
 */
function plugin_assets($plugin_id, $file)
{
    if(is_plugin_enabled($plugin_id)){
        return asset("core/plugins/$plugin_id/assets/$file");
    } else {
        abort(404);
    }
}

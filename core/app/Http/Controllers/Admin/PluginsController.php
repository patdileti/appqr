<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PluginsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $plugins = collect(array_to_object([
            [
                'id' => 'quickorder',
                'name' => 'QuickOrder',
                'description' => 'Allow restaurant owners to receive order details directly on their WhatsApp from the customers.',
                'image' => '',
                'version' => '1.0',
                'min_app_version' => '1.0',
                'url' => 'https://codecanyon.net/item/quickorder-whatsapp-ordering-saas-php-script/30357600',
                'author' => 'Bylancer',
                'author_url' => 'https://bylancer.com/',
            ]
        ]));

        $installed_plugins = get_installed_plugins();

        $plugins = $plugins->map(function ($plugin) use ($installed_plugins) {
            if (array_key_exists($plugin->id, $installed_plugins)) {
                /* Check update available */
                $installed_plugins[$plugin->id]->update_available = false;
                if (version_compare($installed_plugins[$plugin->id]->version, $plugin->version, '<')) {
                    $installed_plugins[$plugin->id]->update_available = true;
                    $installed_plugins[$plugin->id]->update_message = ___('New version available for this plugin.').' (<strong>V'.$plugin->version.'</strong>)';
                }

                $plugin = $installed_plugins[$plugin->id];
                $plugin->installed = true;
            } else {
                $plugin->update_available = false;
                $plugin->installed = false;
                $plugin->enabled = false;
            }

            /* Check plugin compatible */
            $plugin->is_compatible = version_compare(config('appinfo.version'), $plugin->min_app_version, '>=');

            return $plugin;
        });

        return view('admin.plugins.index', compact('plugins', 'installed_plugins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.plugins.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plugin_zip' => ['required', 'mimes:zip'],
        ]);
        if ($validator->fails()) {
            $result = array('success' => false, 'message' => $validator->errors()->first());
            return response()->json($result, 200);
        }

        if (!class_exists('ZipArchive')) {
            $result = array('success' => false, 'message' => ___('ZipArchive extension is not enabled'));
            return response()->json($result, 200);
        }

        try {
            $file = $request->file('plugin_zip');
            $file_name = uniqid().time().'.'.$file->getClientOriginalExtension();

            if (
                !Storage::disk('local')->put("temp/$file_name", file_get_contents($file))
            ) {
                throw new \Exception(___('File upload error.'));
            }

            $plugin_storage_path = storage_path("app/temp/$file_name");

            $plugin_temp_folder = storage_path("app/temp/".uniqid().time());

            if (File::exists($plugin_temp_folder)) {
                File::deleteDirectory($plugin_temp_folder);
            }

        } catch (\Exception $e) {
            $result = array('success' => false, 'message' => $e->getMessage());
            return response()->json($result, 200);
        }

        try {
            $zip = new \ZipArchive;
            $res = $zip->open($plugin_storage_path, \ZipArchive::CREATE);
            if ($res !== true) {
                throw new \Exception(___('Could not open the plugin zip file'));
            }

            $res = $zip->extractTo($plugin_temp_folder);
            if ($res) {
                remove_file($plugin_storage_path);
            }
            $zip->close();

            /* Check plugin is valid */
            $plugin_config = "{$plugin_temp_folder}/plugin.php";
            if (!File::exists($plugin_config)) {
                throw new \Exception(___('Invalid plugin zip file.'));
            }

            $config = array_to_object(require $plugin_config);
            if (empty($config->id)) {
                throw new \Exception(___('Invalid plugin zip file.'));
            }

            /* Check plugin compatibility */
            if (version_compare(config('appinfo.version'), $config->min_app_version, '<')) {
                throw new \Exception(___('This plugin is not compatible with the current version of your script. Update your script to version <strong>:VERSION_CODE</strong> or higher.',
                    ['VERSION_CODE' => $config->min_app_version]));
            }

            $plugin_destination_path = base_path('plugins/'.$config->id);
            if (File::exists($plugin_destination_path)) {
                File::deleteDirectory($plugin_destination_path);
            }

            /* Move the plugin files */
            File::move($plugin_temp_folder, $plugin_destination_path);

            File::deleteDirectory($plugin_temp_folder);

            $result = array('success' => true, 'message' => ___('Plugin installed successfully.'));
            return response()->json($result, 200);

        } catch (\Exception $e) {
            remove_file($plugin_storage_path);
            File::deleteDirectory($plugin_temp_folder);

            $result = array('success' => false, 'message' => $e->getMessage());
            return response()->json($result, 200);
        }
    }

    /**
     * Enable the plugin
     */
    public function enable($id, Request $request)
    {
        if ($plugin = get_plugin($id)) {
            if (!$plugin->enabled) {
                /* Check plugin compatibility */
                if (version_compare(config('appinfo.version'), $plugin->min_app_version, '<')) {
                    quick_alert_error(___('This plugin is not compatible with the current version of your script. Update your script to version <strong>:VERSION_CODE</strong> or higher.',
                        ['VERSION_CODE' => $plugin->min_app_version]));
                    return back();
                }

                /* Enable the plugin */
                $active_plugins = config('settings.active_plugins', []);
                $active_plugins[] = $plugin->id;
                Option::updateOptions('active_plugins', $active_plugins);

                quick_alert_success(___('Plugin is enabled.'));
                return back();
            } else {
                quick_alert_error(___('Plugin is already enabled.'));
                return back();
            }

        } else {
            quick_alert_error(___('Plugin not found.'));
            return back();
        }
    }

    /**
     * Disable the plugin
     */
    public function disable($id, Request $request)
    {
        if ($plugin = get_plugin($id)) {
            if ($plugin->enabled) {
                $active_plugins = config('settings.active_plugins', []);
                unset($active_plugins[array_search($plugin->id, $active_plugins)]);
                Option::updateOptions('active_plugins', $active_plugins);

                quick_alert_success(___('Plugin is disabled.'));
                return back();
            } else {
                quick_alert_error(___('Plugin is already disabled.'));
                return back();
            }

        } else {
            quick_alert_error(___('Plugin not found.'));
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        if ($plugin = get_plugin($id)) {

            if ($plugin->enabled) {
                quick_alert_error(___('Plugin is enabled, disable it first.'));
                return back();
            }

            File::deleteDirectory(base_path('plugins/'.$plugin->id));
            quick_alert_success(___('Deleted Successfully'));

            return back();
        } else {
            quick_alert_error(___('Plugin not found.'));
            return back();
        }
    }
}

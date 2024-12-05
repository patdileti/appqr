<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\Option;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InstallController extends Controller
{
    /**
     * Display the index page - Step 1
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('install.welcome');
    }

    /**
     * Display the requirements page - Step 2
     *
     * @return \Illuminate\View\View
     */
    public function requirements()
    {
        $array = config('install.extensions');

        $results = [];

        foreach ($array as $type => $value) {
            if ($type == 'php') {
                foreach ($value as $extensions) {
                    if (!extension_loaded($extensions)) {
                        $results['extensions'][$type][$extensions] = false;
                        $results['errors'] = true;
                    } else {
                        $results['extensions'][$type][$extensions] = true;
                    }
                }
            } elseif ($type == 'apache') {
                foreach ($value as $modules) {
                    if (function_exists('apache_get_modules')) {
                        if (!in_array($modules, apache_get_modules())) {
                            $results['extensions'][$type][$modules] = false;
                            $results['errors'] = true;
                        } else {
                            $results['extensions'][$type][$modules] = true;
                        }
                    }
                }
            }
        }

        if (version_compare(PHP_VERSION, config('install.php_version')) == -1) {
            $results['errors'] = true;
        }

        return view('install.requirements', ['results' => $results]);
    }

    /**
     * Display the Permissions page. - Step 3
     *
     * @return \Illuminate\View\View
     */
    public function permissions()
    {
        $array = config('install.permissions');

        $results = [];
        foreach ($array as $type => $files) {
            foreach ($files as $file) {
                if (is_writable(base_path('../'.$file))) {
                    $results['permissions'][$type][$file] = true;
                } else {
                    $results['permissions'][$type][$file] = false;
                    $results['errors'] = true;
                }
            }
        }

        return view('install.permissions', ['results' => $results]);
    }

    /**
     * Display the Database details page. - Step 4
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        return view('install.database');
    }

    /**
     * Display old database update notice - Step 4 (1)
     *
     * @return \Illuminate\View\View
     */
    public function updateDatabase()
    {
        return view('install.update-database');
    }

    /**
     * Display the Admin details page. - Step 5
     *
     * @return \Illuminate\View\View
     */
    public function admin()
    {
        return view('install.admin', ['db_empty' => $this->isDBEmpty()]);
    }

    /**
     * Display the Complete page. - Step 6
     *
     * @return \Illuminate\View\View
     */
    public function complete()
    {
        return view('install.complete');
    }

    /**
     * Check the database details and update the .env file.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateEnv(Request $request)
    {
        $request->validate(
            [
                'database_hostname' => ['required', 'string', 'max:50'],
                'database_port' => ['required', 'numeric'],
                'database_name' => ['required', 'string', 'max:50'],
                'database_prefix' => ['nullable', 'string', 'max:20'],
                'database_username' => ['required', 'string', 'max:50'],
                'database_password' => ['nullable', 'string', 'max:50'],
            ]
        );

        $response = $this->validateDatabaseDetails($request);
        if ($response !== true) {
            return back()->with('error', ___('Database details invalid').' '.$response)->withInput();
        }

        $response = $this->updateEnvFile($request);
        if ($response !== true) {
            return back()->with('error',
                ___('.env file is not writable, check file permissions.').' '.$response)->withInput();
        }

        /* Check database is empty or not */
        if (!$this->isDBEmpty()) {
            return redirect()->route('install.update_database');
        }

        return redirect()->route('install.admin');
    }

    /**
     * Migrate the database and create the admin user.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createAdmin(Request $request)
    {
        $rules = [
            'purchase_code' => ['required', 'string', 'max:50']
        ];

        $db_empty = $this->isDBEmpty();

        if ($db_empty) {
            $rules = array_merge($rules, [
                'name' => ['required', 'string', 'max:50'],
                'username' => ['required', 'string', 'min:2', 'max:50'],
                'email' => ['required', 'string', 'email', 'max:100'],
                'password' => ['required', 'string', 'min:6', 'max:128', 'confirmed'],
            ]);
        }

        $request->validate($rules);

        $result = $this->validatePurchase($request);
        if ($result !== true) {
            $result = !empty($result) ? $result : ___('Connection error, please try again later.');
            return back()->with('error', $result)->withInput();
        }

        $response = $this->updateOldDatabase();
        if ($response !== true) {
            return back()->with('error', ___('Database update failed').' '.$response)->withInput();
        }

        $response = $this->migrateDatabase();
        if ($response !== true) {
            return back()->with('error', ___('Database migration failed.').' '.$response)->withInput();
        }

        Option::updateOptions('purchase_code', $request->input('purchase_code'));

        $response = $this->createAdminUser($request, $db_empty);
        if ($response !== true) {
            return back()->with('error', ___('Admin user creation failed.').' '.$response)->withInput();
        }

        $response = $this->installed();
        if ($response !== true) {
            return back()->with('error', ___('.env file update failed.').' '.$response)->withInput();
        }

        $admin_username = null;
        if (!$db_empty) {
            /* Show admin details if the DB was not empty */
            $admin_username = User::where('user_type', 'admin')->first()->username;
        }

        return redirect()->route('install.complete', ['admin_username' => $admin_username]);
    }

    /**
     * Validate the database details.
     *
     * @return bool|string
     */
    private function validateDatabaseDetails(Request $request)
    {
        $settings = config("database.connections.mysql");

        config([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => array_merge($settings, [
                        'driver' => 'mysql',
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                        'prefix' => $request->input('database_prefix')
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            DB::connection()->getPdo();

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Update the .env file.
     *
     * @return bool|string
     */
    private function updateEnvFile(Request $request)
    {
        try {
            set_env('APP_KEY', 'base64:'.base64_encode(Str::random(32)));
            set_env('APP_URL', route('home'));
            set_env('APP_DEBUG', 'false');
            set_env('DEMO_MODE', 'false');
            set_env('APP_VERSION', config('appinfo.version'));
            set_env('DB_HOST', $request->input('database_hostname'));
            set_env('DB_PORT', $request->input('database_port'));
            set_env('DB_DATABASE', $request->input('database_name'));
            set_env('DB_PREFIX', $request->input('database_prefix'));
            set_env('DB_USERNAME', $request->input('database_username'));
            set_env('DB_PASSWORD', $request->input('database_password'));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Check DB is empty or not
     *
     * @return bool
     */
    private function isDBEmpty()
    {
        return !Schema::hasTable('user');
    }

    /**
     * Validate Purchase
     *
     * @param $request
     * @return bool|string
     */
    private function validatePurchase(Request $request)
    {
        try {
            $response = Http::get('https://bylancer.com/api/api.php', [
                "verify-purchase" => $request->input('purchase_code'),
                "ip" => $request->ip(),
                "site_url" => route('home'),
                "version" => config('appinfo.version'),
                "script" => config('appinfo.name'),
                "email" => $request->input('email')
            ]);

            if ($response->ok()) {
                $result = $response->json();

                if ($result['success']) {
                    return true;
                } else {
                    return $result['error'];
                }

            } else {
                return false;
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Migrate the database.
     * @return bool|string
     */
    private function migrateDatabase()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Artisan::call('migrate', ['--force' => true]);
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Create the admin user.
     *
     * @param  Request  $request
     * @param  bool  $db_empty
     * @return bool|string
     */
    private function createAdminUser(Request $request, $db_empty)
    {
        try {
            $ipInfo = user_ip_info();

            if ($db_empty) {
                /* Create admin user if the DB was empty */

                $user = User::create([
                    'user_type' => 'admin',
                    'name' => $request->input('name'),
                    'username' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => Hash::make($request->input('password')),
                    'country' => $ipInfo->location->country_code,
                    'country_code' => $ipInfo->location->country,
                ]);
                $user->markEmailAsVerified();
            } else {

                $prefix = DB::getTablePrefix();

                /* Get all the admins from the old DB */
                $admins = DB::select("SELECT * FROM `{$prefix}admins`");
                foreach ($admins as $admin) {

                    if ($user = User::where('email', $admin->email)->first()) {
                        /* if a user exits with the same email then change the user type to 'admin' */
                        $user->update(['user_type' => 'admin']);
                    } elseif (User::where('username', $admin->username)->exists()) {

                        $username = $admin->username.'_admin';
                        $user = User::create([
                            'user_type' => 'admin',
                            'name' => $admin->name,
                            'username' => $username,
                            'email' => $admin->email,
                            'password' => $admin->password_hash,
                            'image' => $admin->image ?? 'default_user.png',
                            'country' => $ipInfo->location->country_code,
                            'country_code' => $ipInfo->location->country,
                        ]);
                    } else {
                        $user = User::create([
                            'user_type' => 'admin',
                            'name' => $admin->name,
                            'username' => $admin->username,
                            'email' => $admin->email,
                            'password' => $admin->password_hash,
                            'image' => $admin->image ?? 'default_user.png',
                            'country' => $ipInfo->location->country_code,
                            'country_code' => $ipInfo->location->country,
                        ]);
                    }

                    $user->markEmailAsVerified();

                    /* Update the admin id in blog table */
                    Blog::where('author', $admin->id)->update(['author' => $user->id]);

                    /* Update the admin id in blog_comments table */
                    BlogComment::where('user_id', $admin->id)
                        ->where('is_admin', '1')->update(['user_id' => $user->id]);
                }
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Write the installed status to the .env file.
     *
     * @return bool|string
     */
    private function installed()
    {
        try {
            set_env('APP_INSTALLED', 'true');
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        return true;
    }

    /**
     * Update old database if exists
     * @return bool|string
     */
    private function updateOldDatabase()
    {
        /* Check database is empty or not */
        if (!$this->isDBEmpty()) {

            try {

                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                $prefix = DB::getTablePrefix();

                DB::unprepared(
                    "ALTER TABLE `{$prefix}allergies`
                                CHANGE `image` `image` VARCHAR(255) NULL DEFAULT 'default.png';
                            ALTER TABLE `{$prefix}allergies`
                                ADD `translations` LONGTEXT NULL DEFAULT NULL AFTER `title`;"
                );
                DB::table("allergies")
                    ->whereNull('image')
                    ->update([
                        'image' => 'default.png'
                    ]);

                if (!Schema::hasColumn('blog', 'slug')) {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}blog`
                                    ADD `slug` VARCHAR(255) NULL DEFAULT NULL AFTER `title`;"
                    );
                }

                DB::unprepared(
                    "ALTER TABLE `{$prefix}blog_comment`
                                CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT NULL;
                            ALTER TABLE `{$prefix}blog_comment`
                                ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;"
                );

                DB::unprepared("UPDATE `{$prefix}countries` SET `name` = `asciiname`");

                DB::unprepared(
                    "ALTER TABLE `{$prefix}faq_entries`
                                   CHANGE `faq_id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;"
                );

                /* Change user_id to restaurant_id */
                $image_menus = DB::select("SELECT * FROM `{$prefix}image_menu`");
                foreach ($image_menus as $menu) {
                    $restaurant = DB::selectOne("SELECT `id` FROM `{$prefix}restaurant` WHERE `user_id` = {$menu->user_id}");
                    if ($restaurant) {
                        DB::unprepared(
                            "UPDATE `{$prefix}image_menu` SET `user_id` = {$restaurant->id} WHERE `id` = {$menu->id}"
                        );
                    }
                }
                DB::unprepared(
                    "RENAME TABLE `{$prefix}image_menu` TO `{$prefix}image_menus`;
                            ALTER TABLE `{$prefix}image_menus`
                                CHANGE `user_id` `restaurant_id` INT(11) NULL DEFAULT NULL"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}languages`
                                    ADD `position` INT(11) NOT NULL DEFAULT '999' AFTER `active`;"
                );

                /* Change user_id to restaurant_id */
                $menus = DB::select("SELECT * FROM `{$prefix}menu`");
                foreach ($menus as $menu) {
                    $restaurant = DB::selectOne("SELECT `id` FROM `{$prefix}restaurant` WHERE `user_id` = {$menu->user_id}");
                    if ($restaurant) {
                        DB::unprepared(
                            "UPDATE `{$prefix}menu` SET `user_id` = {$restaurant->id} WHERE `id` = {$menu->id}"
                        );
                    }
                }
                DB::unprepared(
                    "ALTER TABLE `{$prefix}menu`
                                CHANGE `user_id` `restaurant_id` INT(11) NULL DEFAULT NULL,
                                CHANGE `cat_id` `category_id` INT(11) NULL DEFAULT NULL;"
                );

                try {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                } catch (\Exception $e) {
                    /* Set translation null to prevent errors */
                    DB::unprepared("UPDATE `{$prefix}menu` SET `translation` = NULL");
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                }

                /* Change user_id to restaurant_id */
                $categories = DB::select("SELECT * FROM `{$prefix}catagory_main`");
                foreach ($categories as $category) {
                    $restaurant = DB::selectOne("SELECT `id` FROM `{$prefix}restaurant` WHERE `user_id` = {$category->user_id}");
                    if ($restaurant) {
                        DB::unprepared(
                            "UPDATE `{$prefix}catagory_main` SET `user_id` = {$restaurant->id} WHERE `cat_id` = {$category->cat_id}"
                        );
                    }
                }
                DB::unprepared(
                    "RENAME TABLE `{$prefix}catagory_main` TO `{$prefix}menu_categories`;
                            ALTER TABLE `{$prefix}menu_categories`
                                CHANGE `cat_id` `id` INT(11) NOT NULL AUTO_INCREMENT,
                                CHANGE `user_id` `restaurant_id` INT(11) NULL DEFAULT NULL,
                                CHANGE `cat_name` `name` VARCHAR(255) NULL DEFAULT NULL,
                                CHANGE `cat_order` `position` INT(11) NULL DEFAULT 999,
                                CHANGE `parent` `parent` INT(11) NULL DEFAULT NULL;"
                );
                try {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_categories`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                } catch (\Exception $e) {
                    /* Set translation null to prevent errors */
                    DB::unprepared("UPDATE `{$prefix}menu_categories` SET `translation` = NULL");
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_categories`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                }
                DB::table("menu_categories")
                    ->where('parent', 0)
                    ->update([
                        'parent' => null
                    ]);

                try {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_extras`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                } catch (\Exception $e) {
                    /* Set translation null to prevent errors */
                    DB::unprepared("UPDATE `{$prefix}menu_extras` SET `translation` = NULL");
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_extras`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                }


                try {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_variant_options`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                } catch (\Exception $e) {
                    /* Set translation null to prevent errors */
                    DB::unprepared("UPDATE `{$prefix}menu_variant_options` SET `translation` = NULL");
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}menu_variant_options`
                                CHANGE `translation` `translations` LONGTEXT NULL DEFAULT NULL;"
                    );
                }

                DB::unprepared(
                    "RENAME TABLE `{$prefix}payments` TO `{$prefix}payment_gateways`;
                            ALTER TABLE `{$prefix}payment_gateways`
                               CHANGE `payment_id` `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT;
                            UPDATE `{$prefix}payment_gateways`
                                SET `payment_folder` = 'two_checkout'
                                WHERE `payment_folder` = '2checkout';"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}plans`
                                CHANGE `monthly_price` `monthly_price` DOUBLE NULL DEFAULT NULL,
                                CHANGE `annual_price` `annual_price` DOUBLE NULL DEFAULT NULL,
                                CHANGE `lifetime_price` `lifetime_price` DOUBLE NULL DEFAULT NULL,
                                CHANGE `status` `status` TINYINT(4) NOT NULL DEFAULT '1',
                                CHANGE `date` `created_at` TIMESTAMP NULL DEFAULT NULL;
                            ALTER TABLE `{$prefix}plans`
                                ADD `updated_at`   TIMESTAMP NULL DEFAULT NULL AFTER `created_at`,
                                ADD `description`  TEXT      NULL DEFAULT NULL AFTER `name`,
                                ADD `translations` LONGTEXT  NULL DEFAULT NULL AFTER `description`,
                                ADD `position`     INT(11)   NULL DEFAULT NULL AFTER `taxes_ids`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}plan_options`
                                ADD `translations` LONGTEXT  NULL DEFAULT NULL AFTER `title`,
                                ADD `created_at`   TIMESTAMP NULL DEFAULT NULL AFTER `active`,
                                ADD `updated_at`   TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}restaurant`
                                ADD `updated_at` DATETIME NULL DEFAULT NULL AFTER `created_at`,
                                ADD `color` VARCHAR(10) NULL DEFAULT NULL AFTER `slug`,
                                ADD `phone` VARCHAR(255) NULL DEFAULT NULL AFTER `address`,
                                CHANGE `name` `title` VARCHAR(255) NOT NULL;
                            RENAME TABLE `{$prefix}restaurant` TO `{$prefix}posts`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}restaurant_options`
                                CHANGE `restaurant_id` `post_id` INT(11) NULL DEFAULT NULL;
                            RENAME TABLE `{$prefix}restaurant_options` TO `{$prefix}post_options`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}restaurant_view`
                                CHANGE `restaurant_id` `post_id` INT(11) NULL DEFAULT NULL;
                            RENAME TABLE `{$prefix}restaurant_view` TO `{$prefix}post_views`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}taxes`
                                CHANGE `datetime` `created_at` TIMESTAMP NULL DEFAULT NULL;
                            ALTER TABLE `{$prefix}taxes`
                                ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;"
                );

                DB::unprepared(
                    "ALTER TABLE `{$prefix}testimonials`
                                ADD `translations` LONGTEXT  NULL DEFAULT NULL AFTER `image`,
                                ADD `created_at`   TIMESTAMP NULL DEFAULT NULL AFTER `translations`,
                                ADD `updated_at`   TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;"
                );

                if (!Schema::hasColumn('transaction', 'details')) {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}transaction`
                                    ADD `details` TEXT NULL DEFAULT NULL AFTER `taxes_ids`;"
                    );
                }
                if (!Schema::hasColumn('transaction', 'currency_code')) {
                    DB::unprepared(
                        "ALTER TABLE `{$prefix}transaction`
                                    ADD `currency_code` VARCHAR(3) NULL DEFAULT NULL AFTER `base_amount`;"
                    );
                }

                DB::unprepared(
                    "ALTER TABLE `{$prefix}transaction`
                                ADD `coupon`     TEXT      NULL DEFAULT NULL AFTER `details`,
                                ADD `created_at` TIMESTAMP NULL DEFAULT NULL AFTER `coupon`,
                                ADD `updated_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
                            ALTER TABLE `{$prefix}transaction`
                                CHANGE `seller_id` `user_id` TEXT NULL DEFAULT NULL;"
                );
                DB::unprepared("UPDATE `{$prefix}transaction` SET `transaction_method` = 'membership' WHERE `transaction_method` = 'Subscription'");

                $transactions = DB::select("SELECT * FROM `{$prefix}transaction`");
                foreach ($transactions as $transaction) {
                    $created = Carbon::createFromTimestamp($transaction->transaction_time);
                    DB::unprepared(
                        "UPDATE `{$prefix}transaction` SET `created_at` = '{$created}', `updated_at` = '{$created}' WHERE `id` = {$transaction->id}"
                    );
                }

                DB::unprepared(
                    "ALTER TABLE `{$prefix}upgrades`
                                CHANGE `interval` `interval` ENUM ('MONTHLY', 'YEARLY', 'LIFETIME') NULL DEFAULT NULL;"
                );

                /* update user type to user because it is not required here */
                DB::unprepared("UPDATE `{$prefix}user` SET `user_type` = 'user'");

                DB::unprepared(
                    "ALTER TABLE `{$prefix}user`
                                CHANGE `user_type` `user_type` ENUM('user','admin') NULL DEFAULT 'user',
                                CHANGE `password_hash` `password` VARCHAR(255)  NULL DEFAULT NULL;
                            ALTER TABLE `{$prefix}user`
                                ADD `email_verified_at` TIMESTAMP    NULL DEFAULT NULL AFTER `oauth_uid`,
                                ADD `remember_token`    VARCHAR(100) NULL DEFAULT NULL AFTER `email_verified_at`;"
                );

                DB::unprepared("UPDATE `{$prefix}user` SET `email_verified_at` = '".Carbon::now()."' WHERE `status` = '1';");
                DB::unprepared("UPDATE `{$prefix}user` SET `status` = '1' WHERE `status` = '0';");
                DB::unprepared("UPDATE `{$prefix}user` SET `status` = '0' WHERE `status` = '2';");

                DB::unprepared(
                    "ALTER TABLE `{$prefix}user`
                                CHANGE `status` `status` TINYINT(1) NOT NULL DEFAULT '1';"
                );

                /* Update restaurant details */
                $users = DB::select("SELECT `id`,`phone`,`currency` FROM `{$prefix}user`");
                foreach ($users as $user) {

                    DB::unprepared(
                        "UPDATE `{$prefix}posts` SET `phone` = '{$user->phone}' WHERE `user_id` = {$user->id}"
                    );

                    $currency = DB::selectOne("SELECT * FROM `{$prefix}currencies` WHERE `code` = '{$user->currency}'");

                    if ($currency) {
                        $post = DB::selectOne("SELECT `id` FROM `{$prefix}posts` WHERE `user_id` = {$user->id}");
                        if ($post) {
                            DB::table("post_options")
                                ->insert([
                                        [
                                            'post_id' => $post->id,
                                            'option_name' => 'currency_sign',
                                            'option_value' => $currency->code
                                        ],
                                        [
                                            'post_id' => $post->id,
                                            'option_name' => 'currency_code',
                                            'option_value' => $currency->code
                                        ],
                                        [
                                            'post_id' => $post->id,
                                            'option_name' => 'currency_pos',
                                            'option_value' => $currency->in_left
                                        ]
                                    ]
                                );
                        }
                    }
                }

                // Update restaurant color
                try {
                    $postOptions = DB::select("SELECT * FROM `{$prefix}post_options` WHERE `option_name` = 'restaurant_color'");
                    if ($postOptions) {
                        foreach ($postOptions as $postOption) {
                            DB::unprepared(
                                "UPDATE `{$prefix}posts` SET `color` = '{$postOption->option_value}' WHERE `id` = {$postOption->post_id}"
                            );
                        }
                    }
                } catch (\Exception $e) {}

                /* Update restaurant slug if not available */
                $posts = DB::select("SELECT * FROM `{$prefix}posts`");
                foreach ($posts as $post) {
                    if(empty($post->slug)){
                        $slug = SlugService::createSlug(Post::class, 'slug', $post->title);
                        DB::unprepared(
                            "UPDATE `{$prefix}posts` SET `slug` = '{$slug}' WHERE `id` = {$post->id}"
                        );
                    }
                }

                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            } catch (\Exception $e) {
                return $e->getMessage();
            }
        }

        return true;
    }
}

<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\Language;
use App\Models\Notification;
use App\Models\Option;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\View\ViewServiceProvider as ConcreteViewServiceProvider;

class ViewServiceProvider extends ConcreteViewServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $activeTheme = active_theme();

        if (env('APP_INSTALLED')) {

            view()->composer('*', function ($view) {
                $settings = Option::all()->pluck('option_value', 'option_name');
                foreach ($settings as $key => $value) {
                    $settings[$key] = Str::isJson($value) ? json_decode($value) : $value;
                }
                $view->with([
                    'settings' => array_to_object($settings),
                    'activeTheme' => active_theme(),
                    'activeThemeAssets' => active_theme(true)
                ]);
            });

            if (is_admin_url()) {

                /* Admin pages */
                view()->composer('*', function ($view) {
                    $admin_languages = Language::where('active', 1)->orderBy('position')->get();
                    $view->with('admin_languages', $admin_languages);
                });

                view()->composer('admin.includes.header', function ($view) {
                    $notifications = Notification::orderbyDesc('id')->limit(20)->get();
                    $totalUnreadNotifications = Notification::where('status', 0)->get()->count();
                    $view->with([
                        'notifications' => $notifications,
                        'totalUnreadNotifications' => $totalUnreadNotifications,
                    ]);
                });

                view()->composer('admin.includes.sidebar', function ($view) {
                    $totalUnapprovedComments = BlogComment::where('active', 0)->count();
                    $view->with([
                        'totalUnapprovedComments' => ($totalUnapprovedComments > 50) ? "50+" : $totalUnapprovedComments,
                    ]);
                });

                view()->composer('admin.users.userdetails', function ($view) {
                    $plans = Plan::where('status', 1)->get();
                    $view->with([
                        'plans' => $plans,
                    ]);
                });

            } else {
                /* Frontend pages */

                view()->composer('*', function ($view) {
                    $languages = Language::where('active', 1)->orderBy('position', 'asc')->get();
                    $view->with('languages', $languages);
                });

                view()->composer($activeTheme . 'blog.sidebar', function ($view) {
                    $blogCategories = BlogCategory::withCount('blogs')->get();
                    $recentBlogs = Blog::where('status', 'publish')->orderbyDesc('id')->limit(3)->get();

                    $tags = [];
                    $data = Blog::where('status', 'publish')->select('tags')->get();
                    foreach ($data as $value) {
                        if (!empty($value->tags)) {
                            $tag = explode(',', $value->tags);
                            $tags = array_merge($tags, $tag);
                        }
                    }
                    $tags = array_unique($tags);

                    $testimonials = Testimonial::limit(5)->get();

                    $view->with(['blogCategories' => $blogCategories, 'recentBlogs' => $recentBlogs, 'blogTags' => $tags, 'testimonials' => $testimonials]);
                });

                view()->composer($activeTheme . 'layouts.includes.footer', function ($view) {
                    $pages = Page::where('active', 1)->where(
                        function ($query) {
                            $query->where('translation_lang', get_lang())
                                ->orWhereNull('translation_lang');
                        }
                    )->get();
                    $view->with('pages', $pages);
                });

            }

        }
    }
}

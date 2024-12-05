<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Mails\RestaurantOrder;
use App\Models\Allergy;
use App\Models\ImageMenu;
use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuExtra;
use App\Models\MenuVariant;
use App\Models\MenuVariantOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemExtra;
use App\Models\Post;
use App\Models\PostOption;
use App\Models\PostView;
use App\Models\Transaction;
use App\Models\WaiterCall;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view($this->activeTheme.'.user.posts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $templates = $this->getPostTemplates();
        return view($this->activeTheme.'.user.posts.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return JsonResponse|RedirectResponse
     */
    public function store(Request $request)
    {
        if (!config('settings.non_active_allow') && !$request->user()->hasVerifiedEmail()) {
            quick_alert_error(___('Verify your email address to post any content.'));
            return back()->withInput();
        }

        $validator = Validator::make($request->all(), [
            'color' => ['required'],
            'cover_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'main_image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'slug' => ['nullable', 'alpha_dash', 'unique:posts'],
            'title' => ['required', 'string', 'max:150'],
            'sub_title' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'address' => ['required', 'string', 'max:255'],
            'timing' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'restaurant_template' => ['required'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            quick_alert_error(implode('<br>', $errors));
            return back()->withInput();
        }

        $cover_image = image_upload($request->file('cover_image'), 'storage/restaurant/cover/', '300');
        $main_image = image_upload($request->file('main_image'), 'storage/restaurant/logo/', '200x200');

        $post = Post::create([
            'user_id' => request()->user()->id,
            'color' => $request->color,
            'title' => $request->title,
            'slug' => !empty($request->slug)
                ? $request->slug
                : SlugService::createSlug(Post::class, 'slug', $request->title),
            'sub_title' => $request->sub_title,
            'description' => $request->description,
            'timing' => $request->timing,
            'phone' => $request->phone,
            'address' => $request->address,
            'main_image' => $main_image,
            'cover_image' => $cover_image,
        ]);

        $default_options = [
            'restaurant_template' => $request->restaurant_template,
            'allow_call_waiter' => 1,
            'restaurant_on_table_order' => 1,
            'restaurant_send_order_notification' => 1,
            'currency_code' => config('settings.currency_code'),
            'currency_sign' => config('settings.currency_sign'),
            'currency_pos' => config('settings.currency_pos'),
        ];
        foreach ($default_options as $key => $value) {
            PostOption::updatePostOption($post->id, $key, $value);
        }

        quick_alert_success(___('Saved Successfully.'));

        return redirect()->route('restaurants.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $restaurant
     */
    public function edit(Post $restaurant)
    {
        $user = request()->user();
        if ($restaurant->user_id == $user->id) {
            $postOptions = post_options($restaurant->id);

            $templates = $this->getPostTemplates();

            $post = $restaurant;

            return view($this->activeTheme.'.user.posts.edit', compact(
                'user',
                'post',
                'postOptions',
                'templates'
            ));
        } else {
            return abort(404);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $restaurant
     */
    public function update(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {

            if (!config('settings.non_active_allow') && !$request->user()->hasVerifiedEmail()) {
                quick_alert_error(___('Verify your email address to post any content.'));
                return back()->withInput();
            }

            $validator = Validator::make($request->all(), [
                'color' => ['required'],
                'cover_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'main_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
                'slug' => ['nullable', 'alpha_dash', 'unique:posts,slug,'.$restaurant->id],
                'title' => ['required', 'string', 'max:150'],
                'sub_title' => ['nullable', 'string', 'max:255'],
                'description' => ['required', 'string'],
                'address' => ['required', 'string', 'max:255'],
                'timing' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'restaurant_template' => ['required'],
                'default_language' => ['required_with:menu_languages'],
                'currency_code' => ['required', 'string', 'max:4', 'regex:/^[A-Z]{3}$/'],
                'currency_sign' => ['required', 'string', 'max:4'],
                'currency_pos' => ['required', 'integer', 'min:0', 'max:1'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                quick_alert_error(implode('<br>', $errors));
                return back()->withInput();
            }

            $cover_image = $restaurant->cover_image;
            $main_image = $restaurant->main_image;

            if ($request->has('cover_image')) {
                $cover_image = image_upload($request->file('cover_image'), 'storage/restaurant/cover/', null, null,
                    $cover_image != 'default.png' ? $cover_image : null);
            }
            if ($request->has('main_image')) {
                $main_image = image_upload($request->file('main_image'), 'storage/restaurant/logo/', '200x200', null,
                    $main_image != 'default.png' ? $main_image : null);
            }


            $restaurant->update([
                'color' => $request->color,
                'title' => $request->title,
                'slug' => !empty($request->slug)
                    ? $request->slug
                    : SlugService::createSlug(Post::class, 'slug', $request->title),
                'sub_title' => $request->sub_title,
                'description' => $request->description,
                'timing' => $request->timing,
                'phone' => $request->phone,
                'address' => $request->address,
                'main_image' => $main_image,
                'cover_image' => $cover_image,
            ]);

            PostOption::updatePostOption($restaurant->id, 'menu_languages', json_encode($request->get('menu_languages')));

            $options = [
                'restaurant_template',
                'menu_layout',
                'default_language',
                'currency_code',
                'currency_sign',
                'currency_pos',
                'allow_call_waiter',
                'restaurant_on_table_order',
                'restaurant_takeaway_order',
                'restaurant_delivery_order',
                'restaurant_delivery_charge',
                'restaurant_send_order_notification',
                'restaurant_online_payment',

                'restaurant_paypal_install',
                'restaurant_paypal_title',
                'restaurant_paypal_sandbox_mode',
                'restaurant_paypal_api_client_id',
                'restaurant_paypal_api_secret',
                'restaurant_paypal_api_app_id',

                'restaurant_stripe_install',
                'restaurant_stripe_title',
                'restaurant_stripe_secret_key',
                'restaurant_stripe_publishable_key',

                'restaurant_razorpay_install',
                'restaurant_razorpay_title',
                'restaurant_razorpay_api_key',
                'restaurant_razorpay_secret_key',

                'restaurant_mollie_install',
                'restaurant_mollie_title',
                'restaurant_mollie_api_key',

                'restaurant_paytm_install',
                'restaurant_paytm_title',
                'restaurant_paytm_sandbox_mode',
                'restaurant_paytm_merchant_key',
                'restaurant_paytm_merchant_mid',
                'restaurant_paytm_merchant_website',

                'restaurant_paystack_install',
                'restaurant_paystack_title',
                'restaurant_paystack_secret_key',
                'restaurant_paystack_public_key',

                'restaurant_payumoney_install',
                'restaurant_payumoney_title',
                'restaurant_payumoney_sandbox_mode',
                'restaurant_payumoney_merchant_pos_id',
                'restaurant_payumoney_signature_key',
                'restaurant_payumoney_oauth_client_id',
                'restaurant_payumoney_oauth_client_secret',

                'restaurant_iyzico_install',
                'restaurant_iyzico_title',
                'restaurant_iyzico_sandbox_mode',
                'restaurant_iyzico_api_key',
                'restaurant_iyzico_secret_key',

                'restaurant_midtrans_install',
                'restaurant_midtrans_title',
                'restaurant_midtrans_sandbox_mode',
                'restaurant_midtrans_client_key',
                'restaurant_midtrans_server_key',

                'restaurant_paytabs_install',
                'restaurant_paytabs_title',
                'restaurant_paytabs_region',
                'restaurant_paytabs_profile_id',
                'restaurant_paytabs_secret_key',

                'restaurant_telr_install',
                'restaurant_telr_title',
                'restaurant_telr_sandbox_mode',
                'restaurant_telr_store_id',
                'restaurant_telr_authkey',

                'restaurant_2checkout_install',
                'restaurant_2checkout_title',
                'restaurant_2checkout_sandbox_mode',
                'restaurant_2checkout_account_number',
                'restaurant_2checkout_public_key',
                'restaurant_2checkout_private_key',

                'restaurant_ccavenue_install',
                'restaurant_ccavenue_title',
                'restaurant_ccavenue_merchant_key',
                'restaurant_ccavenue_access_code',
                'restaurant_ccavenue_working_key',
            ];

            foreach ($options as $option) {
                PostOption::updatePostOption($restaurant->id, $option, $request->get($option));
            }

            quick_alert_success(___('Updated Successfully.'));

            return back();
        }

        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $restaurant
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $restaurant->delete();
        }
        $result = array(
            'success' => true,
            'message' => ___('Deleted Successfully')
        );
        return response()->json($result, 200);

    }

    /**
     * Show the QR builder page
     *
     * @param  Post  $restaurant
     * @return \Illuminate\Contracts\View\View
     */
    public function qrBuilder(Post $restaurant)
    {
        $post = $restaurant;
        if ($restaurant->user_id == request()->user()->id) {

            $post_options = post_options($restaurant->id);
            return view($this->activeTheme.'.user.posts.qr-builder', compact('post', 'post_options'));
        }

        abort(404);
    }

    /**
     * Update QR Details
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function qrBuilderSave(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'qr_image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back()->withInput();
            }

            $qr_image = post_options($restaurant->id, 'qr_image');

            if ($request->has('qr_image')) {
                $qr_image = image_upload($request->file('qr_image'), 'storage/restaurant/logo/', '150x150', null,
                    $qr_image);
                PostOption::updatePostOption($restaurant->id, 'qr_image', $qr_image);
            }

            $requestData = $request->except('submit', 'qr_image', '_token');
            foreach ($requestData as $key => $value) {
                PostOption::updatePostOption($restaurant->id, $key, $value);
            }

            quick_alert_success(___('Saved Successfully'));
            return back();
        } else {
            quick_alert_error(___('Unexpected Error'));
            return back()->withInput();
        }
    }

    /**
     * Show the Menu page
     *
     * @param  Post  $restaurant
     * @return \Illuminate\Contracts\View\View
     */
    public function menu(Post $restaurant)
    {
        $post = $restaurant;
        if ($restaurant->user_id == request()->user()->id) {

            if (post_options($restaurant->id, 'restaurant_template') != 'flipbook') {
                $allergies = Allergy::query()
                    ->where('active', '1')
                    ->orderBy('position')
                    ->get();

                $menu_languages = $default_menu_language = [];
                if(post_options($restaurant->id, 'menu_languages')
                    && post_options($restaurant->id, 'default_language')) {
                    $menu_languages = Language::query()
                        ->whereIn('code', post_options($restaurant->id, 'menu_languages'))
                        ->get();
                    if (!empty($menu_languages)) {
                        if (!empty($_COOKIE['Quick_user_lang_code'])) {
                            /* Get default language by cookie if user changed it */
                            $default_menu_language = $menu_languages
                                ->where('code', $_COOKIE['Quick_user_lang_code'])
                                ->first();
                        }
                        if (empty($default_menu_language)) {
                            /* Get default language */
                            $default_menu_language = $menu_languages
                                ->where('code', post_options($restaurant->id, 'default_language'))
                                ->first();
                        }
                    }
                }

                return view($this->activeTheme.'.user.posts.menu.index',
                    compact('post', 'allergies', 'menu_languages', 'default_menu_language'));
            } else {
                return view($this->activeTheme.'.user.posts.menu.image', compact('post'));
            }
        }

        abort(404);
    }

    /**
     * Add New Category
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return RedirectResponse
     */
    public function addCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            $limit = request()->user()->plan()->settings->category_limit;
            if ($limit != "999") {
                $total = count($restaurant->menu_categories);

                if ($total >= $limit) {
                    quick_alert_error(___('Limit exceeded, please upgrade your membership.'));
                    return back();
                }
            }

            MenuCategory::create([
                'name' => $request->get('name'),
                'restaurant_id' => $restaurant->id
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Update Category
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return RedirectResponse
     */
    public function updateCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            $category = MenuCategory::where('id', $request->get('id'))
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $name = $category->name;
            $translations = $category->translations ?? new \stdClass();
            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                $translations->{$_COOKIE['Quick_user_lang_code']} = ['title' => $request->get('name')];

                /* Update default value if the language is default */
                if ($_COOKIE['Quick_user_lang_code'] == post_options($restaurant->id, 'default_language')) {
                    $name = $request->get('name');
                }
            } else {
                $name = $request->get('name');
            }

            $category->update([
                'name' => $name,
                'translations' => $translations
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Delete Category
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function deleteCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            MenuCategory::where('id', $request->get('id'))
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail()
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function reorderCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    MenuCategory::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Add New Sub Category
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return RedirectResponse
     */
    public function addSubCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'parent' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            $limit = request()->user()->plan()->settings->category_limit;
            if ($limit != "999") {
                $total = count($restaurant->menu_categories);

                if ($total >= $limit) {
                    quick_alert_error(___('Limit exceeded, please upgrade your membership.'));
                    return back();
                }
            }

            MenuCategory::create([
                'name' => $request->get('name'),
                'restaurant_id' => $restaurant->id,
                'parent' => $request->get('parent')
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Update Sub Category
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return RedirectResponse
     */
    public function updateSubCategory(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'parent' => ['required', 'integer'],
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            $category = MenuCategory::where('id', $request->get('id'))
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail();

            $name = $category->name;
            $translations = $category->translations ?? new \stdClass();
            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                $translations->{$_COOKIE['Quick_user_lang_code']} = ['title' => $request->get('name')];

                /* Update default value if the language is default */
                if ($_COOKIE['Quick_user_lang_code'] == post_options($restaurant->id, 'default_language')) {
                    $name = $request->get('name');
                }
            } else {
                $name = $request->get('name');
            }

            $category->update([
                'parent' => $request->get('parent'),
                'name' => $name,
                'translations' => $translations
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Add New Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function addMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'category_id' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'max:255'],
                'type' => ['required', 'in:veg,nonveg'],
                'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $limit = request()->user()->plan()->settings->menu_limit;
            if ($limit != "999") {
                $total = Menu::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->where('category_id', $request->get('category_id'))
                    ->count();

                if ($total >= $limit) {
                    $result = array(
                        'success' => false, 'message' => ___('Limit exceeded, please upgrade your membership.')
                    );
                    return response()->json($result, 200);
                }
            }

            $image = 'default.png';
            if ($request->has('image')) {
                $image = image_upload($request->file('image'), 'storage/menu/', '200x200');
            }

            Menu::create([
                'restaurant_id' => $restaurant->id,
                'name' => $request->get('name'),
                'description' => $request->get('description'),
                'type' => $request->get('type'),
                'image' => $image,
                'price' => $request->get('price'),
                'category_id' => $request->get('category_id'),
                'allergies' => $request->has('allergies')
                    ? implode(',', $request->get('allergies'))
                    : null,
                'active' => $request->has('active') ? '1' : '0',
            ]);

            $result = array('success' => true, 'message' => ___('Saved Successfully'));
            return response()->json($result, 200);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Update Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function updateMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'category_id' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0'],
                'description' => ['nullable', 'string', 'max:255'],
                'type' => ['required', 'in:veg,nonveg'],
                'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $menu = Menu::findOrFail($request->get('id'));

            $image = $menu->image;
            if ($request->has('image')) {
                $image = image_upload($request->file('image'), 'storage/menu/', '200x200', null,
                    $image != 'default.png' ? $image : null);
            }

            $name = $menu->name;
            $description = $menu->description;
            $translations = $menu->translations ?? new \stdClass();
            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                $translations->{$_COOKIE['Quick_user_lang_code']} = [
                    'title' => $request->get('name'),
                    'description' => $request->get('description'),
                ];

                /* Update default value if the language is default */
                if ($_COOKIE['Quick_user_lang_code'] == post_options($restaurant->id, 'default_language')) {
                    $name = $request->get('name');
                    $description = $request->get('description');
                }
            } else {
                $name = $request->get('name');
                $description = $request->get('description');
            }

            $menu->update([
                'name' => $name,
                'description' => $description,
                'type' => $request->get('type'),
                'image' => $image,
                'price' => $request->get('price'),
                'category_id' => $request->get('category_id'),
                'allergies' => $request->has('allergies')
                    ? implode(',', $request->get('allergies'))
                    : null,
                'active' => $request->has('active') ? '1' : '0',
                'translations' => $translations
            ]);

            $result = array('success' => true, 'message' => ___('Saved Successfully'));
            return response()->json($result, 200);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Delete Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function deleteMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            Menu::where('id', $request->get('id'))
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail()
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function reorderMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    Menu::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Show the Menu Extras page
     *
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return View
     */
    public function menuItemExtras(Post $restaurant, Menu $menu)
    {
        $post = $restaurant;
        if ($restaurant->user_id == request()->user()->id) {

            $variants = [];
            $variant_options = $menu->variantOptions;

            foreach ($menu->variants as $variant) {
                $title = [];
                foreach ($variant->options as $id => $value) {
                    $variant_option = $variant_options->find($id);
                    if (isset($variant_option->options[$value])) {
                        $title[] = $variant_option->options[$value];
                    }
                }
                $variant->title = implode(', ', $title);
                $variants[] = $variant;
            }

            $menu_languages = $default_menu_language = [];
            if(post_options($restaurant->id, 'menu_languages')
                && post_options($restaurant->id, 'default_language')) {
                $menu_languages = Language::query()
                    ->whereIn('code', post_options($restaurant->id, 'menu_languages'))
                    ->get();
                if (!empty($menu_languages)) {
                    if (!empty($_COOKIE['Quick_user_lang_code'])) {
                        /* Get default language by cookie if user changed it */
                        $default_menu_language = $menu_languages
                            ->where('code', $_COOKIE['Quick_user_lang_code'])
                            ->first();
                    }
                    if (empty($default_menu_language)) {
                        /* Get default language */
                        $default_menu_language = $menu_languages
                            ->where('code', post_options($restaurant->id, 'default_language'))
                            ->first();
                    }
                }
            }

            return view($this->activeTheme.'.user.posts.menu.extras',
                compact('post', 'menu', 'variants', 'menu_languages', 'default_menu_language'));
        }

        abort(404);
    }

    /**
     * Add New Variant Option
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return RedirectResponse
     */
    public function menuAddVariantOption(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255'],
                'options' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            $options = explode(',', $request->get('options'));
            $options = array_map('trim', $options);

            MenuVariantOption::create([
                'title' => $request->get('title'),
                'options' => $options,
                'menu_id' => $menu->id
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Update Variant Option
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return JsonResponse
     */
    public function menuUpdateVariantOption(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'title' => ['required', 'string', 'max:255'],
                'options' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result);
            }

            $options = explode(',', $request->get('options'));
            $options = array_map('trim', $options);

            $variantOption = MenuVariantOption::findOrFail($request->get('id'));

            $title = $variantOption->title;
            $op = $variantOption->options;
            $translations = $variantOption->translations ?? new \stdClass();
            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                $translations->{$_COOKIE['Quick_user_lang_code']} = [
                    'title' => $request->get('title'),
                    'options' => $options,
                ];

                /* Update default value if the language is default */
                if ($_COOKIE['Quick_user_lang_code'] == post_options($restaurant->id, 'default_language')) {
                    $title = $request->get('title');
                    $op = $options;
                }
            } else {
                $title = $request->get('title');
                $op = $options;
            }

            $variantOption->update([
                'title' => $title,
                'options' => $op,
                'active' => $request->has('active'),
                'translations' => $translations,
            ]);

            $result = array('success' => true, 'message' => ___('Saved Successfully'));
            return response()->json($result);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result);
        }
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function menuReorderVariantOption(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    MenuVariantOption::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Delete Menu Variant
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function menuDeleteVariantOption(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            MenuVariantOption::where('id', $request->get('id'))
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Add New Variant
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return RedirectResponse
     */
    public function menuAddVariant(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'price' => ['required', 'numeric', 'min:0']
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            MenuVariant::create([
                'price' => $request->get('price'),
                'options' => $request->get('options'),
                'menu_id' => $menu->id
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Update Variant
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     */
    public function menuUpdateVariant(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'price' => ['required', 'numeric', 'min:0']
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result);
            }

            MenuVariant::findOrFail($request->get('id'))
                ->update([
                    'price' => $request->get('price'),
                    'options' => $request->get('options'),
                    'active' => $request->has('active')
                ]);

            $title = [];
            foreach ($request->get('options') as $id => $value) {
                $variant_option = $menu->variantOptions->find($id);
                if (isset($variant_option->options[$value])) {
                    $title[] = $variant_option->options[$value];
                }
            }

            $result = array(
                'success' => true, 'message' => ___('Saved Successfully'), 'title' => implode(', ', $title)
            );
            return response()->json($result);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result);
        }
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function menuReorderVariant(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    MenuVariant::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Delete Menu Variant
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function menuDeleteVariant(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            MenuVariant::where('id', $request->get('id'))
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Add New Extra Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return RedirectResponse
     */
    public function menuAddExtra(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'title' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0']
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    quick_alert_error($error);
                }
                return back();
            }

            MenuExtra::create([
                'title' => $request->get('title'),
                'price' => $request->get('price'),
                'menu_id' => $menu->id
            ]);

            quick_alert_success(___('Saved Successfully'));
            return back();

        } else {
            quick_alert_error(___('Unexpected Error'));
            return back();
        }
    }

    /**
     * Update Extra Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @param  Menu  $menu
     * @return JsonResponse
     */
    public function menuUpdateExtra(Request $request, Post $restaurant, Menu $menu)
    {
        if ($restaurant->user_id == request()->user()->id) {

            $validator = Validator::make($request->all(), [
                'id' => ['required'],
                'title' => ['required', 'string', 'max:255'],
                'price' => ['required', 'numeric', 'min:0']
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result);
            }

            $extra = MenuExtra::findOrFail($request->get('id'));

            $title = $extra->title;
            $translations = $extra->translations ?? new \stdClass();
            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                $translations->{$_COOKIE['Quick_user_lang_code']} = ['title' => $request->get('title')];

                /* Update default value if the language is default */
                if ($_COOKIE['Quick_user_lang_code'] == post_options($restaurant->id, 'default_language')) {
                    $title = $request->get('title');
                }
            } else {
                $title = $request->get('title');
            }

            $extra->update([
                'title' => $title,
                'price' => $request->get('price'),
                'active' => $request->has('active'),
                'translations' => $translations,
            ]);

            $result = array(
                'success' => true, 'message' => ___('Saved Successfully')
            );
            return response()->json($result);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result);
        }
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function menuReorderExtra(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    MenuExtra::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Delete Menu Extra
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function menuDeleteExtra(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            MenuExtra::where('id', $request->get('id'))
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Add New Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function addImageMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'image' => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $limit = request()->user()->plan()->settings->menu_limit;
            if ($limit != "999") {
                $total = ImageMenu::query()
                    ->where('restaurant_id', $restaurant->id)
                    ->count();

                if ($total >= $limit) {
                    $result = array(
                        'success' => false, 'message' => ___('Limit exceeded, please upgrade your membership.')
                    );
                    return response()->json($result, 200);
                }
            }

            $image = image_upload($request->file('image'), 'storage/menu/', '1000');

            ImageMenu::create([
                'restaurant_id' => $restaurant->id,
                'name' => $request->get('name'),
                'image' => $image,
                'active' => $request->has('active') ? '1' : '0',
            ]);

            $result = array('success' => true, 'message' => ___('Saved Successfully'));
            return response()->json($result, 200);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Update Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function updateImageMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
                'name' => ['required', 'string', 'max:255'],
                'image' => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = array('success' => false, 'message' => implode('<br>', $errors));
                return response()->json($result, 200);
            }

            $menu = ImageMenu::findOrFail($request->get('id'));

            $image = $menu->image;
            if ($request->has('image')) {
                $image = image_upload($request->file('image'), 'storage/menu/', '1000', null,
                    $image != 'default.png' ? $image : null);
            }

            $menu->update([
                'name' => $request->get('name'),
                'image' => $image,
                'active' => $request->has('active') ? '1' : '0',
            ]);

            $result = array('success' => true, 'message' => ___('Saved Successfully'));
            return response()->json($result, 200);

        } else {
            $result = array('success' => false, 'message' => ___('Unexpected Error'));
            return response()->json($result, 200);
        }
    }

    /**
     * Delete Menu Item
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function deleteImageMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $validator = Validator::make($request->all(), [
                'id' => ['required', 'integer'],
            ]);
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                $result = ['success' => false, 'message' => implode('<br>', $errors)];
                return response()->json($result);
            }

            ImageMenu::where('id', $request->get('id'))
                ->where('restaurant_id', $restaurant->id)
                ->firstOrFail()
                ->delete();

            $result = ['success' => true, 'message' => ___('Deleted Successfully')];
            return response()->json($result);
        }

        $result = ['success' => false, 'message' => ___('Unexpected Error')];
        return response()->json($result);
    }

    /**
     * Reorder resources
     *
     * @return JsonResponse
     */
    public function reorderImageMenuItem(Request $request, Post $restaurant)
    {
        if ($restaurant->user_id == request()->user()->id) {
            $position = $request->position;
            if (is_array($request->position)) {
                foreach ($position as $index => $id) {
                    ImageMenu::where('id', $id)->update([
                        'position' => $index,
                    ]);
                }
            }
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Show the Order page
     *
     * @param  Post|null  $restaurant
     */
    public function orders(Post $restaurant = null)
    {
        if (!$restaurant) {
            $restaurant = request()->user()->posts->first();

            if ($restaurant) {
                return to_route('restaurants.orders', $restaurant->id);
            }
        }

        if(!$restaurant){
            quick_alert_error(___('No restaurants available.') .' '. ___('Add New Restaurant'));
            return redirect()->route('restaurants.create');
        }

        $post = $restaurant;
        if ($restaurant->user_id == request()->user()->id) {

            $postOptions = post_options($restaurant->id);

            /* Update currency */
            config(['settings.currency_sign' => @$postOptions->currency_sign]);
            config(['settings.currency_pos' => @$postOptions->currency_pos]);
            config(['settings.currency_code' => @$postOptions->currency_code]);

            if (request()->ajax()) {
                /* Get only unseen orders for ajax */
                $orders = Order::query()
                    ->with('items')
                    ->where('restaurant_id', $restaurant->id)
                    ->whereNot('status', 'unpaid')
                    ->where('seen', 0)
                    ->latest()
                    ->get();
            } else {
                $orders = Order::query()
                    ->with('items')
                    ->where('restaurant_id', $restaurant->id)
                    ->whereNot('status', 'unpaid')
                    ->latest()
                    ->paginate(50);
            }

            foreach ($orders as $key => $order) {

                $price = 0;
                if ($order->items) {
                    foreach ($order->items as $item) {
                        if($item->menu) {
                            /* Menu Variants */
                            $variant_title = array();
                            if ($item->variant) {
                                $item->menu->price = $item->variant->price;

                                foreach ($item->variant->options as $option_id => $option_key) {
                                    $variant_option = MenuVariantOption::find($option_id);

                                    $variant_title[] = $variant_option['options'][$option_key];
                                }
                            }

                            $variant_title = !empty($variant_title) ? ' ('.implode(', ', $variant_title).')' : '';
                            $item->variant_title = $variant_title;

                            $price += $item->menu->price * $item->quantity;

                            /* Menu Extras */
                            if ($item->itemExtras) {
                                foreach ($item->itemExtras as $itemExtra) {
                                    $price += $itemExtra->extra->price * $item->quantity;
                                }
                            }
                        }
                    }
                }

                if($price == 0){
                    /* Remove order if menus not available */
                    $orders->forget($key);
                } else {

                    if ($order->type == 'delivery') {
                        $price += $postOptions->restaurant_delivery_charge;
                    }
                    $order->price = $price;
                }


            }

            /* Mark all the orders as seen */
            Order::query()
                ->whereIn('id', $orders->pluck('id')->toArray())
                ->update([
                    'seen' => 1
                ]);

            /* Return orders if it's ajax */
            if (request()->ajax()) {
                $data = [];
                foreach ($orders as $order) {
                    $data[] = view(
                        $this->activeTheme.'.user.posts.orders.order-row',
                        compact('postOptions', 'order')
                    )->render();
                }

                $result = array('success' => true, 'orders' => $data);
                return response()->json($result, 200);
            }

            /* Delete old unpaid orders */
            Order::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('status', 'unpaid')
                ->where('created_at', '<=', Carbon::now()->subHours(3))
                ->delete();

            $menu_languages = $default_menu_language = [];
            if(@$postOptions->menu_languages && @$postOptions->default_language) {
                $menu_languages = Language::query()
                    ->whereIn('code', $postOptions->menu_languages)
                    ->get();
                if (!empty($menu_languages)) {
                    if (!empty($_COOKIE['Quick_user_lang_code'])) {
                        /* Get default language by cookie if user changed it */
                        $default_menu_language = $menu_languages
                            ->where('code', $_COOKIE['Quick_user_lang_code'])
                            ->first();
                    }
                    if (empty($default_menu_language)) {
                        /* Get default language */
                        $default_menu_language = $menu_languages
                            ->where('code', $postOptions->default_language)
                            ->first();
                    }
                }
            }

            return view($this->activeTheme.'.user.posts.orders.index',
                compact('post', 'postOptions', 'menu_languages', 'default_menu_language', 'orders'));
        }

        abort(404);
    }

    /**
     * Mark Complete an order
     *
     * @return JsonResponse
     */
    public function completeOrder(Request $request, Order $order)
    {
        if ($order->restaurant->user_id == request()->user()->id) {
            $order->status = 'completed';
            $order->save();
        }

        $result = array('success' => true, 'message' => ___('Updated Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Delete an order
     *
     * @return JsonResponse
     */
    public function deleteOrder(Request $request, Order $order)
    {
        if ($order->restaurant->user_id == request()->user()->id) {
            $order->delete();
        }

        $result = array('success' => true, 'message' => ___('Deleted Successfully'));
        return response()->json($result, 200);
    }

    /**
     * Heartbeat
     *
     * @return JsonResponse
     */
    public function heartbeat(Request $request)
    {
        /* Check orders available */
        $orders = Order::query()
            ->whereIn('restaurant_id', $request->user()->posts->pluck('id')->toArray())
            ->where('seen', 0)
            ->whereNot('status', 'unpaid')
            ->count();

        if ($orders) {
            /* Mark as seen */
            Order::query()
                ->whereIn('restaurant_id', $request->user()->posts->pluck('id')->toArray())
                ->where('seen', 0)
                ->whereNot('status', 'unpaid')
                ->update(['seen' => 1]);
        }

        /* Get Waiter Calls */
        $calls = WaiterCall::query()
            ->whereIn('restaurant_id', $request->user()->posts->pluck('id')->toArray())
            ->where('seen', 0)
            ->get();

        $notifications = [];
        foreach ($calls as $call) {
            $notifications[] = '<small><i class="far fa-utensils"></i> '.$call->restaurant->title.'</small>'
                .'<br>'.
                '<i class="fa fa-bell"></i> '.___('Customer is waiting at table number').' '.$call->table_no;
        }

        /* Delete Waiter Calls */
        WaiterCall::query()
            ->whereIn('id', $calls->pluck('id')->toArray())
            ->delete();

        $result = array('success' => true, 'orders' => $orders, 'waiterCalls' => $notifications);
        return response()->json($result, 200);
    }

    /**
     * @return array
     */
    private function getPostTemplates(): array
    {
        $templates = [];
        $temPaths = array_filter(glob(base_path().'/resources/views/post_templates/*'), 'is_dir');
        foreach ($temPaths as $temp) {
            $arr = explode('/', $temp);
            $tempname = end($arr);

            $filepath = public_path('assets/post_templates/'.$tempname.'/theme-info.txt');
            if (file_exists($filepath)) {
                $themefile = fopen($filepath, "r");

                $themeinfo = array();
                while (!feof($themefile)) {
                    $lineRead = fgets($themefile);
                    if (strpos($lineRead, ':') !== false) {
                        $line = explode(':', $lineRead);
                        $key = trim($line[0]);
                        $value = trim($line[1]);
                        $themeinfo[$key] = $value;
                    }
                }

                $templates[$tempname]['folder'] = $tempname;
                $templates[$tempname]['name'] = $themeinfo['Theme Name'];
                $templates[$tempname]['image'] = asset('assets/post_templates/'.$tempname.'/preview.png');

                fclose($themefile);
            }
        }
        return $templates;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     */
    public function publicView($slug)
    {
        /* Get post by id for old urls */
        if(is_numeric($slug)){
            $post = Post::where('id', $slug)->first();
            if(!$post){
                $post = Post::where('slug', $slug)->first();
            }
        } else {
            $post = Post::where('slug', $slug)->first();
        }

        if ($post && $post->user->status == 1) {

            /* check user's plan is enabled */
            $plan = $post->user->plan();
            if ($plan->status) {

                // check for url
                if (request()->has('qr-id')) {
                    $qr_id = quick_xor_decrypt(request()->get('qr-id'), 'quick-qr');
                    if ($slug == $qr_id) {

                        if ($plan->settings->scan_limit != "999") {

                            $total = PostView::where('date', '>=', Carbon::now()->startOfMonth())
                                ->where('post_id', $post->id)
                                ->count();

                            if ($total >= $plan->settings->scan_limit) {
                                quick_alert_error(___("Scan Limit Exceed"));
                                return redirect()->route('home');
                            }
                        }

                        PostView::create([
                            'post_id' => $post->id,
                            'ip' => request()->ip(),
                            'date' => date('Y-m-d H:i:s')
                        ]);

                        return redirect()->route('publicView', $post->slug);
                    }
                }

                $postOptions = post_options($post->id);
                if (empty($postOptions->restaurant_template)) {
                    $postOptions->restaurant_template = 'classic-theme';
                }

                $theme = $postOptions->restaurant_template;

                if($theme == 'flipbook') {
                    return view('post_templates.'.$theme.'.index', compact(
                        'post',
                        'theme',
                        'postOptions',
                        'plan',
                    ));
                } else {

                    $menu_languages = $default_menu_language = [];
                    if(@$postOptions->menu_languages && @$postOptions->default_language) {
                        $menu_languages = Language::query()
                            ->whereIn('code', @$postOptions->menu_languages)
                            ->get();
                        if (!empty($menu_languages)) {
                            if (!empty($_COOKIE['Quick_user_lang_code'])) {
                                /* Get default language by cookie if user changed it */
                                $default_menu_language = $menu_languages
                                    ->where('code', $_COOKIE['Quick_user_lang_code'])
                                    ->first();
                            }
                            if (empty($default_menu_language)) {
                                /* Get default language */
                                $default_menu_language = $menu_languages
                                    ->where('code', @$postOptions->default_language)
                                    ->first();
                            }

                            /* Change language of main site */
                            App::setLocale($default_menu_language->code);
                            Session::forget('locale');
                            Session::put('locale', $default_menu_language->code);
                        }
                    }

                    $allow_order = $allow_on_table = $allow_takeaway = $allow_delivery = $allow_payment = 0;

                    if ($plan->settings->allow_ordering) {
                        $allow_on_table = @$postOptions->restaurant_on_table_order;
                        $allow_takeaway = @$postOptions->restaurant_takeaway_order;
                        $allow_delivery = @$postOptions->restaurant_delivery_order;

                        $allow_payment = config('settings.admin_allow_online_payment') ? @$postOptions->restaurant_online_payment : 0;

                        $allow_order = $allow_on_table || $allow_takeaway || $allow_delivery;
                    }

                    $allergies = config('settings.admin_allergies')
                        ? Allergy::where('active', '1')->get()
                        : collect();

                    /* Get all menus with extra items */
                    $total_menus = [];
                    $menus = Menu::with([
                        'variantOptions' => function ($q) {
                            $q->where('active', 1);
                        },
                        'variants' => function ($q) {
                            $q->where('active', 1);
                        },
                        'extras' => function ($q) {
                            $q->where('active', 1);
                        }
                    ])
                        ->where('restaurant_id', $post->id)
                        ->where('active', '1')
                        ->orderBy('position')
                        ->get();

                    foreach ($menus as $menu) {
                        $total_menus[$menu->id] = $menu;
                    }

                    /* Update currency */
                    config(['settings.currency_sign' => @$postOptions->currency_sign]);
                    config(['settings.currency_pos' => @$postOptions->currency_pos]);
                    config(['settings.currency_code' => @$postOptions->currency_code]);

                    return view('post_templates.'.$theme.'.index', compact(
                        'post',
                        'theme',
                        'postOptions',
                        'plan',
                        'menu_languages',
                        'default_menu_language',
                        'allow_order',
                        'allow_on_table',
                        'allow_takeaway',
                        'allow_delivery',
                        'allow_payment',
                        'allergies',
                        'total_menus',
                    ));
                }
            }
        }

        return abort(404);
    }

    /**
     * Call The Waiter
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function callTheWaiter(Request $request, Post $restaurant)
    {
        $validator = Validator::make($request->all(), [
            'table' => ['required', 'integer'],
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = ['success' => false, 'message' => implode('<br>', $errors)];
            return response()->json($result);
        }

        WaiterCall::create([
            'restaurant_id' => $restaurant->id,
            'table_no' => $request->get('table')
        ]);

        $result = ['success' => true, 'message' => ___('Saved Successfully')];
        return response()->json($result);
    }

    /**
     * Send Order
     *
     * @param  Request  $request
     * @param  Post  $restaurant
     * @return JsonResponse
     */
    public function sendOrder(Request $request, Post $restaurant)
    {
        $validator = Validator::make($request->all(), [
            'items' => ['required'],
            'name' => ['required', 'string', 'max:255'],
            'ordering-type' => ['required', 'in:on-table,takeaway,delivery'],
            'table' => ['required_if:ordering-type,on-table'],
            'phone-number' => ['required_unless:ordering-type,on-table'],
            'address' => ['required_if:ordering-type,delivery'],
            'pay_via' => ['required']
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $errors[] = $error;
            }
            $result = ['success' => false, 'message' => implode('<br>', $errors)];
            return response()->json($result);
        }

        $order = new Order();
        $order->restaurant_id = $restaurant->id;
        $order->type = $request->get('ordering-type');
        $order->customer_name = $request->get('name');

        $customer_details = $request->get('name')."\n";

        $icon_menu_item = "▪️";
        $icon_menu_extra = "▫️";
        $icon_phone = "☎️";
        $icon_hash = "#️⃣";
        $icon_address = "📌";
        $icon_message = "📝";

        $order_type = '';
        $amount = $delivery_charge = 0;
        if ($request->get('ordering-type') == 'on-table') {
            /* on table */
            $order->table_number = $request->get('table');

            $customer_details .= $icon_hash.' '.$request->get('table');

            $order_type = ___('On table');
        } else {
            if ($request->get('ordering-type') == 'takeaway') {
                /* takeaway */
                $order->phone_number = $request->get('phone-number');

                $customer_details .= $icon_phone.' '.$request->get('phone-number');

                $order_type = ___('Takeaway');
            } else {
                if ($request->get('ordering-type') == 'delivery') {
                    /* delivery */
                    $order->phone_number = $request->get('phone-number');
                    $order->address = $request->get('address');

                    $customer_details .= $icon_phone.' '.$request->get('phone-number')."\n";
                    $customer_details .= $icon_address.' '.$request->get('address');

                    $order_type = ___('Delivery');
                    $delivery_charge = post_options($restaurant->id, 'restaurant_delivery_charge', 0);
                }
            }
        }

        if (!empty($_POST['message'])) {
            $customer_details .= "\n".$icon_message.' '.$request->get('message')."\n";
        }

        $order->message = $request->get('message');
        $order->created_at = Carbon::now();

        if ($request->get('pay_via') == 'pay_online') {
            $order->status = 'unpaid';
        }

        $order->save();

        $items = json_decode($request->get('items'));
        $order_msg = $order_whatsapp_detail = '';

        foreach ($items as $item) {
            $item_id = $item->id;
            $quantity = $item->quantity;
            $variants = $item->variants;

            $menu = Menu::find($item_id);

            if ($menu) {
                /* save order items */
                $order_item = OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $item_id,
                    'quantity' => $quantity,
                    'variation' => is_numeric($variants) ? $variants : 0,
                ]);

                $variant_title = array();
                if (is_numeric($variants)) {
                    $menu_variant = MenuVariant::query()
                        ->where('menu_id', $item_id)
                        ->find($variants);

                    if ($menu_variant) {
                        $menu->price = $menu_variant->price;


                        foreach ($menu_variant->options as $option_id => $option_key) {
                            $menu_variant_option = MenuVariantOption::find($option_id);

                            $options = $menu_variant_option->getOriginal('options');
                            $variant_title[] = $options[$option_key];
                        }
                    }
                }
                $variant_title = !empty($variant_title) ? ' ('.implode(', ', $variant_title).')' : '';

                $amount += $menu->price * $quantity;

                $order_msg .= $menu->getOriginal('name').$variant_title.($quantity > 1 ? ' &times; '.$quantity : '').'<br>';

                $order_whatsapp_detail .= $icon_menu_item.$menu->getOriginal('name').$variant_title.' X '.$quantity."\n";

                $extras = $item->extras;
                foreach ($extras as $extra) {
                    $menu_extra = MenuExtra::find($extra->id);

                    if ($menu_extra) {
                        // save order items extras
                        OrderItemExtra::create([
                            'order_item_id' => $order_item->id,
                            'extra_id' => $extra->id,
                        ]);

                        $amount += $menu_extra->price * $quantity;

                        $order_msg .= $menu_extra->getOriginal('title').'<br>';

                        $order_whatsapp_detail .= "\t".$icon_menu_extra.$menu_extra->getOriginal('title')."\n";
                    }
                }
                $order_msg .= '<br>';
            }
        }
        $amount += $delivery_charge;

        /* Update currency */
        config(['settings.currency_sign' => post_options($restaurant->id, 'currency_sign')]);
        config(['settings.currency_pos' => post_options($restaurant->id, 'currency_pos')]);
        config(['settings.currency_code' => post_options($restaurant->id, 'currency_code')]);

        /* Send email to restaurant owner */
        if (post_options($restaurant->id, 'restaurant_send_order_notification', 1)) {

            $restaurant->user->sendMail(new RestaurantOrder([
                'restaurant_name' => $restaurant->title,
                'customer_name' => $request->get('name'),
                'table_number' => $request->get('table'),
                'phone_number' => $request->get('phone-number'),
                'address' => $request->get('address'),
                'order_type' => $order_type,
                'order' => $order_msg,
                'message' => $request->get('message'),
            ]));
        }

        $result = ['success' => true, 'message' => '', 'whatsapp_url' => ''];

        /* Whatsapp Ordering Plugin */
        if(is_plugin_enabled('quickorder') && post_options($restaurant->id, 'quickorder_enable')){
            $whatsapp_number = post_options($restaurant->id, 'whatsapp_number');
            $whatsapp_message = post_options($restaurant->id, 'whatsapp_message');

            if (empty($whatsapp_message))
                $whatsapp_message = config('settings.quickorder_whatsapp_message');

            $short_codes = [
                '{ORDER_ID}' => $order->id,
                '{ORDER_DETAILS}' => $order_whatsapp_detail,
                '{CUSTOMER_DETAILS}' => $customer_details,
                '{ORDER_TYPE}' => $order_type,
                '{ORDER_TOTAL}' => price_code_format($amount)
            ];

            $whatsapp_message = str_replace(array_keys($short_codes), array_values($short_codes), $whatsapp_message);

            $result['whatsapp_url'] = 'https://api.whatsapp.com/send?phone=' . $whatsapp_number . '&text=' . urlencode($whatsapp_message);
        }

        if ($request->get('pay_via') == 'pay_online') {

            /* Create transaction to store all the details for further actions */
            $transaction = Transaction::create([
                'product_name' => $restaurant->title.' (#'.$order->id.')',
                'product_id' => $order->id,
                'user_id' => $restaurant->id,
                'base_amount' => $amount,
                'amount' => $amount,
                'currency_code' => post_options($restaurant->id, 'currency_code'),
                'transaction_method' => 'order',
                'transaction_ip' => $request->ip(),
                'transaction_description' => $restaurant->title.' (#'.$order->id.')',
                'details' => [
                    'whatsapp_url' => $result['whatsapp_url'],
                    'customer_name' => $request->get('name'),
                    'phone' => $request->get('phone-number'),
                ]
            ]);

            $result['message'] = route('payment.index', $transaction->id);
        }

        return response()->json($result);
    }
}

<?php

namespace App\Http\Controllers;

use App;
use App\Models\Blog;
use App\Models\Faq;
use App\Models\Language;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Session;
use Validator;

class HomeController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->activeTheme = active_theme();
    }

    /**
     * Display the home page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        if (config('settings.disable_landing_page')) {
            return redirect()->route('login');
        }
        /* Redirect to dashboard if user login */
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $testimonials = Testimonial::limit(10)->get();
        $blogArticles = Blog::with('user')->where('status', 'publish')->limit(3)->get();
        $faqs = Faq::where('active', 1)->where(
            function ($query) {
                $query->where('translation_lang', get_lang())
                    ->orWhereNull('translation_lang');
            }
        )->limit(10)->get();

        $plans = Plan::where('status', 1)->get();
        $total_monthly = $plans->sum('monthly_price');
        $total_annual = $plans->sum('annual_price');
        $total_lifetime = $plans->sum('lifetime_price');

        $free_plan = config('settings.free_membership_plan');
        $trial_plan = config('settings.trial_membership_plan');

        /* Whatsapp Ordering Plugin */
        if (is_plugin_enabled('quickorder') && config('settings.quickorder_homepage_enable')) {
            return view('quickorder::home')->with(compact(
                'testimonials',
                'faqs',
                'blogArticles',
                'plans',
                'total_monthly',
                'total_annual',
                'total_lifetime',
                'free_plan',
                'trial_plan'));
        } else {
            return view($this->activeTheme.'.home.index')->with(compact(
                'testimonials',
                'faqs',
                'blogArticles',
                'plans',
                'total_monthly',
                'total_annual',
                'total_lifetime',
                'free_plan',
                'trial_plan'));
        }
    }

    /**
     * Display the pricing page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pricing()
    {
        $plans = Plan::where('status', 1)->get();
        $total_monthly = $plans->sum('monthly_price');
        $total_annual = $plans->sum('annual_price');
        $total_lifetime = $plans->sum('lifetime_price');

        $free_plan = config('settings.free_membership_plan');
        $trial_plan = config('settings.trial_membership_plan');

        return view($this->activeTheme.'.home.pricing', compact(
            'plans',
            'total_monthly',
            'total_annual',
            'total_lifetime',
            'free_plan',
            'trial_plan'));
    }

    /**
     * Display the faq page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function faqs()
    {
        abort_if(!config('settings.enable_faqs'), 404);

        $faqs = Faq::where('active', 1)->where(
            function ($query) {
                $query->where('translation_lang', get_lang())
                    ->orWhereNull('translation_lang');
            }
        )->paginate(20);
        return view($this->activeTheme.'.home.faqs', compact('faqs'));
    }

    /**
     * Display the testimonials page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function testimonials()
    {
        abort_if(!config('settings.testimonials_enable'), 404);

        $testimonials = Testimonial::paginate(21);
        return view($this->activeTheme.'.home.testimonials', compact('testimonials'));
    }

    /**
     * Display the static page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function page($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('active', 1)
            ->where(
                function ($query) {
                    $query->where('translation_lang', get_lang())
                        ->orWhereNull('translation_lang');
                }
            )->firstOrFail();

        abort_if($page->type == 1 && !auth()->check(), 404);

        return view($this->activeTheme.'.home.page', compact('page'));
    }

    /**
     * Display the contact page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function contact()
    {
        return view($this->activeTheme.'.home.contact');
    }

    /**
     * Handle contact requests
     */
    public function contactSend(Request $request)
    {
        if (!config('settings.contact_email')) {
            quick_alert_error(___('Email sending is disabled.'));
            return back();
        }

        $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
            ] + validate_recaptcha());

        try {
            $name = $request->name;
            $email = $request->email;

            $short_codes = [
                '{SITE_TITLE}' => config('settings.site_title'),
                '{SITE_URL}' => route('home'),
                '{NAME}' => $name,
                '{EMAIL}' => $email,
                '{CONTACT_SUBJECT}' => $request->subject,
                '{MESSAGE}' => nl2br($request->message),
            ];

            $subject = str_replace(array_keys($short_codes), array_values($short_codes),
                config('settings.email_sub_contact'));
            $msg = str_replace(array_keys($short_codes), array_values($short_codes),
                config('settings.email_message_contact'));

            \Mail::send([], [], function ($message) use ($msg, $email, $subject, $name) {
                $message->to(config('settings.contact_email'))
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->replyTo($email)
                    ->subject($subject)
                    ->html($msg);
            });

            quick_alert_success(___('Thank you for contacting us.'));
            return back();

        } catch (\Exception$e) {
            quick_alert_error(___('Email sending failed, please try again.'));
            return back();
        }
    }

    /**
     * Display the feedback page
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function feedback()
    {
        return view($this->activeTheme.'.home.feedback');
    }

    /**
     * Handle contact requests
     */
    public function feedbackSend(Request $request)
    {
        if (!config('settings.contact_email')) {
            quick_alert_error(___('Email sending is disabled.'));
            return back();
        }

        $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
                'subject' => ['required', 'string', 'max:255'],
                'message' => ['required', 'string'],
            ] + validate_recaptcha());

        try {
            $name = $request->name;
            $email = $request->email;
            $phone = $request->phone;

            $short_codes = [
                '{SITE_TITLE}' => config('settings.site_title'),
                '{SITE_URL}' => route('home'),
                '{NAME}' => $name,
                '{EMAIL}' => $email,
                '{PHONE}' => $phone,
                '{FEEDBACK_SUBJECT}' => $request->subject,
                '{MESSAGE}' => nl2br($request->message),
            ];

            $subject = str_replace(array_keys($short_codes), array_values($short_codes),
                config('settings.email_sub_feedback'));
            $msg = str_replace(array_keys($short_codes), array_values($short_codes),
                config('settings.email_message_feedback'));

            \Mail::send([], [], function ($message) use ($msg, $email, $subject, $name) {
                $message->to(config('settings.contact_email'))
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
                    ->replyTo($email)
                    ->subject($subject)
                    ->html($msg);
            });

            quick_alert_success(___('Thank you for your feedback.'));
            return back();

        } catch (\Exception$e) {
            quick_alert_error(___('Email sending failed, please try again.'));
            return back();
        }
    }

    /**
     * Handle newsletter requests
     */
    public function newsletter(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:subscriber']
        ]);

        App\Models\Subscriber::create([
            'email' => $request->email,
            'joined' => Carbon::now()
        ]);

        $result = array(
            'success' => true
        );
        return response()->json($result, 200);
    }

    /**
     * Change the language
     *
     * @param $code
     * @return \Illuminate\Http\RedirectResponse
     */
    public function localize($code)
    {
        $language = Language::where('code', $code)->firstOrFail();
        App::setLocale($language->code);
        Session::forget('locale');
        Session::put('locale', $language->code);

        return redirect()->back();
    }
}

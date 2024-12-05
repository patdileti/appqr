<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\Upgrade;
use App\Models\User;
use App\Models\UserOption;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Validator;

class CheckoutController extends Controller
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
     */
    public function index(Request $request)
    {
        abort_if(!$request->get('plan'), 404);

        if (!config('settings.non_active_allow') && !$request->user()->hasVerifiedEmail()) {
            quick_alert_error(___('Your email address is not verified. Please verify your email address to use all the features.'));
            return redirect()->route('pricing');
        }

        $user = request()->user();

        /* check if user already using this plan */
        if ($user->group_id == $request->get('plan')) {
            quick_alert_error(___('You are already using this plan.'));
            return redirect()->route('pricing');
        }

        /* Update the details first on payment submit */
        if ($request->has('payment_submit')) {

            $validator = Validator::make($request->all(), [
                'type' => ['required', 'string'],
                'tax_id' => ['nullable', 'string'],
                'name' => ['required', 'string'],
                'address' => ['required', 'string'],
                'city' => ['required', 'string'],
                'state' => ['required', 'string'],
                'zip' => ['required', 'string'],
                'country' => ['required', 'string', 'exists:countries,code'],
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->all() as $error) {
                    $errors[] = $error;
                }
                quick_alert_error(implode('<br>', $errors));
                return back()->withInput();
            }

            /* Update billing details */
            $billing = [
                'billing_details_type' => $request->type,
                'billing_tax_id' => $request->tax_id,
                'billing_name' => $request->name,
                'billing_address' => $request->address,
                'billing_city' => $request->city,
                'billing_state' => $request->state,
                'billing_zipcode' => $request->zip,
                'billing_country' => $request->country,
            ];
            foreach ($billing as $key => $value) {
                UserOption::updateUserOption($user->id, $key, $value);
            }
        }

        $coupon = $interval = null;
        $price = $base_amount = $price_after_discount = 0;
        $applied_tax_ids = $applied_taxes = [];
        $user_options = user_options($user->id);

        if ($request->get('plan') == 'free') {

            quick_alert_error(___('You will get the free plan automatically after your current plan expires.'));
            return redirect()->route('pricing');

        } else {
            if ($request->get('plan') == 'trial') {

                /* check trial done */
                if (user_options($user->id, 'package_trial_done')) {
                    quick_alert_error(___("Your trial option was already used, you can't use it anymore."));
                    return redirect()->route('pricing');
                }

                $plan = config('settings.trial_membership_plan');

                abort_if($plan->status != 1, 404);

                $planEndDate = Carbon::now()->addDays($plan->days);

            } else {

                $plan = Plan::where('status', 1)->findOrFail($request->get('plan'));

                if ($request->input('interval') == 'monthly') {
                    $price = $plan['monthly_price'];
                    $interval = 'MONTHLY';
                    $planEndDate = Carbon::now()->addMonth();
                } else {
                    if ($request->input('interval') == 'yearly') {
                        $price = $plan['annual_price'];
                        $interval = 'YEARLY';
                        $planEndDate = Carbon::now()->addYear();
                    } else {
                        if ($request->input('interval') == 'lifetime') {
                            $price = $plan['lifetime_price'];
                            $interval = 'LIFETIME';
                            $planEndDate = Carbon::now()->addYears(100);
                        } else {
                            /* redirect to monthly interval */
                            return redirect()->route('checkout.index',
                                ['interval' => 'monthly', 'plan' => $request->get('plan')]);
                        }
                    }
                }

                $base_amount = $price;

                /* If coupon was applied */
                if ($request->has('coupon_code')) {
                    $coupon = Coupon::where('code', $request->get('coupon_code'))
                        /* check coupon is not expired */
                        ->where('expiry_at', '>', Carbon::now())
                        ->first();

                    if ($coupon) {
                        if ($coupon->used < $coupon->limit) {
                            $price = $price_after_discount = ($base_amount - ($base_amount * $coupon->percentage) / 100);
                        } else {
                            quick_alert_error(___('Coupon code usage limit exceeded.'));
                            return redirect()->back()->withInput();
                        }
                    } else {
                        quick_alert_error(___('Coupon code is expired or invalid.'));
                        return redirect()->back()->withInput();
                    }

                }

                /* calculate tax */
                if (!empty($plan['taxes_ids']) && $price != 0) {
                    $taxes = Tax::whereIn('id', explode(',', $plan['taxes_ids']))->get();

                    $inclusive_tax = $exclusive_tax = 0;

                    foreach ($taxes as $tax) {

                        /* filter plan taxes */

                        /* Type */
                        if (
                            $tax['billing_type'] != @$user_options->billing_details_type &&
                            $tax['billing_type'] != 'both'
                        ) {
                            continue;
                        }

                        /* Countries */
                        if (
                            $tax['countries'] &&
                            !in_array(@$user_options->billing_country, explode(',', $tax['countries']))
                        ) {
                            continue;
                        }

                        /* calculate inclusive taxes */
                        if ($tax['type'] == 'inclusive') {
                            $inclusive_tax += $tax['value_type'] == 'percentage' ? $price * ($tax['value'] / 100) : $tax['value'];
                        }

                        $applied_taxes[] = $tax;
                        $applied_tax_ids[] = $tax['id'];
                    }

                    $price_without_inclusive = $price - $inclusive_tax;

                    /* calculate exclusive taxes */
                    foreach ($taxes as $tax) {
                        /* filter plan taxes */

                        /* Type */
                        if (
                            $tax['billing_type'] != @$user_options->billing_details_type &&
                            $tax['billing_type'] != 'both'
                        ) {
                            continue;
                        }

                        /* Countries */
                        if (
                            $tax['countries'] &&
                            !in_array(@$user_options->billing_country, explode(',', $tax['countries']))
                        ) {
                            continue;
                        }

                        if ($tax['type'] == 'exclusive') {
                            $exclusive_tax += $tax['value_type'] == 'percentage' ? $price_without_inclusive * ($tax['value'] / 100) : $tax['value'];
                        }
                    }

                    /* total price */
                    $price += $exclusive_tax;
                }

            }
        }


        /* payment form submit */
        if ($request->has('payment_submit')) {

            if ($request->input('plan') == 'trial') {

                /* check trial done */
                if (user_options($user->id, 'package_trial_done')) {
                    quick_alert_error(___("Your trial option was already used, you can't use it anymore."));
                    return redirect()->route('pricing');
                }

                /* Delete old membership */
                Upgrade::where('user_id', $user->id)->delete();

                /* Create new membership */
                Upgrade::create([
                    'sub_id' => 'trial',
                    'user_id' => $user->id,
                    'upgrade_lasttime' => Carbon::now()->timestamp,
                    'upgrade_expires' => $planEndDate->timestamp,
                    'status' => Upgrade::STATUS_ACTIVE,
                ]);

                /* Update user group */
                $user->update([
                    'group_id' => 'trial',
                ]);

                /* Update user trial */
                UserOption::updateUserOption($user->id, 'package_trial_done', 1);

                quick_alert_success(___('Subscribed Successfully'));
                return redirect()->route('subscription');

            } else {

                $transaction_desc = !empty($plan->translations->{get_lang()}->name)
                    ? $plan->translations->{get_lang()}->name
                    : $plan->name;
                $transaction_desc .= ' ('.plan_interval_text($interval).')';

                $transaction = Transaction::create([
                    'product_name' => $plan->name,
                    'product_id' => $plan->id,
                    'user_id' => $user->id,
                    'base_amount' => $base_amount,
                    'amount' => $price,
                    'currency_code' => config('settings.currency_code'),
                    'transaction_method' => 'membership',
                    'transaction_ip' => $request->ip(),
                    'transaction_description' => $transaction_desc,
                    'frequency' => $interval,
                    'billing' => [
                        'type' => @$user_options->billing_details_type,
                        'tax_id' => @$user_options->billing_tax_id,
                        'name' => @$user_options->billing_name,
                        'address' => @$user_options->billing_address,
                        'city' => @$user_options->billing_city,
                        'state' => @$user_options->billing_state,
                        'zipcode' => @$user_options->billing_zipcode,
                        'country' => @$user_options->billing_country
                    ],
                    'taxes_ids' => implode(',', $applied_tax_ids),
                    'coupon' => (
                    $coupon
                        ? ['id' => $coupon->id, 'code' => $coupon->code, 'percentage' => $coupon->percentage]
                        : null
                    ),
                    'status' => ($price == 0 ? 'success' : null)
                ]);

                /* Process zero price subscription */
                if ($price == 0) {

                    /* Increase the coupon usage (100% coupon off) */
                    if($coupon) {
                        $coupon->increment('used');
                    }

                    /* Delete old membership */
                    Upgrade::where('user_id', $user->id)->delete();

                    /* Create new membership */
                    Upgrade::create([
                        'sub_id' => $plan->id,
                        'user_id' => $user->id,
                        'interval' => $interval,
                        'upgrade_lasttime' => Carbon::now()->timestamp,
                        'upgrade_expires' => $planEndDate->timestamp,
                        'status' => Upgrade::STATUS_ACTIVE,
                    ]);

                    /* Update user group */
                    $user->update([
                        'group_id' => $plan->id,
                    ]);

                    quick_alert_success(___('Subscribed Successfully'));
                    return redirect()->route('subscription');
                }

                $gateway = PaymentGateway::where('id', $request->payment_method)->where('payment_install',
                    '1')->firstOrFail();
                $paymentController = __NAMESPACE__.'\PaymentMethods\\'
                    .str_replace(
                        ' ', '',
                        ucwords(
                            str_replace('_', ' ', $gateway->payment_folder))
                    )
                    .'Controller';
                return $paymentController::pay($transaction);
            }
        }

        $paymentGateways = PaymentGateway::where('payment_install', '1')->get();

        $planStartDate = date_formating(Carbon::now());
        $planEndDate = date_formating($planEndDate);

        return view($this->activeTheme.'.user.checkout',
            compact('user', 'plan', 'interval', 'price', 'price_after_discount', 'applied_taxes', 'base_amount',
                'coupon', 'paymentGateways', 'planStartDate', 'planEndDate', 'user_options'));
    }

    /**
     * Update Subscription data
     *
     * @param  Transaction  $transaction
     */
    public static function updateUserPlan(Transaction $transaction)
    {
        /* Increase the coupon usage if exist */
        if ($transaction->coupon) {
            $coupon = Coupon::find($transaction->coupon->id);
            $coupon->increment('used');
        }

        $planEndDate = null;
        if ($transaction->frequency == 'MONTHLY') {
            $planEndDate = Carbon::now()->addMonth();
        } else {
            if ($transaction->frequency == 'YEARLY') {
                $planEndDate = Carbon::now()->addYear();
            } else {
                if ($transaction->frequency == 'LIFETIME') {
                    $planEndDate = Carbon::now()->addYears(100);
                }
            }
        }

        /* Delete old membership */
        Upgrade::where('user_id', $transaction->user->id)->delete();

        /* Create new membership */
        Upgrade::create([
            'sub_id' => $transaction->product_id,
            'user_id' => $transaction->user->id,
            'interval' => $transaction->frequency,
            'upgrade_lasttime' => Carbon::now()->timestamp,
            'upgrade_expires' => $planEndDate->timestamp,
            'status' => Upgrade::STATUS_ACTIVE,
        ]);

        /* Update user group */
        $transaction->user->update([
            'group_id' => $transaction->product_id,
        ]);
    }

    /**
     * Process Webhook
     */
    public static function processWebhook($gateway, $metadata, $payment_id, $subscription_id)
    {

        $user = User::find($metadata->user_id);
        /* Check user exists */
        if (!$user) {
            return response()->json([
                'status' => 400
            ], 400);
        }

        $plan = Plan::find($metadata->product_id);
        /* Check plan exists */
        if (!$plan) {
            return response()->json([
                'status' => 400
            ], 400);
        }

        /* Make sure transaction is not already exist */
        if (Transaction::where('payment_id', $payment_id)
            ->where('transaction_gatway', $gateway)
            ->exists()) {
            return response()->json([
                'status' => 400
            ], 400);
        }

        /* Unsubscribe from the previous plan if exists */
        if ($user->upgrade && !empty($user->upgrade->unique_id) && $user->upgrade->unique_id != $subscription_id) {
            try {
                $user->cancelRecurringSubscription();
            } catch (\Exception $e) {
                Log::info($e->getMessage());

                return response()->json([
                    'message' => $e->getMessage(),
                    'status' => 400
                ], 400);
            }
        }

        $user_options = user_options($user->id);

        /* Create Transaction */
        $transaction = Transaction::create([
            'product_name' => $plan->name,
            'product_id' => $plan->id,
            'user_id' => $user->id,
            'base_amount' => $metadata->base_amount,
            'amount' => $metadata->amount,
            'currency_code' => $metadata->currency_code,
            'transaction_method' => 'membership',
            'transaction_ip' => $metadata->transaction_ip,
            'transaction_description' => $metadata->transaction_description,
            'frequency' => $metadata->frequency,
            'transaction_gatway' => $gateway,
            'payment_id' => $payment_id,
            'billing' => [
                'type' => @$user_options->billing_details_type,
                'tax_id' => @$user_options->billing_tax_id,
                'name' => @$user_options->billing_name,
                'address' => @$user_options->billing_address,
                'city' => @$user_options->billing_city,
                'state' => @$user_options->billing_state,
                'zipcode' => @$user_options->billing_zipcode,
                'country' => @$user_options->billing_country
            ],
            'taxes_ids' => $metadata->taxes_ids,
            'coupon' => $metadata->coupon,
            'status' => Transaction::STATUS_SUCCESS
        ]);

        /* Increase the coupon usage if exist */
        if ($transaction->coupon) {
            $coupon = Coupon::find($transaction->coupon->id);
            $coupon->increment('used');
        }

        $planEndDate = null;
        if ($transaction->frequency == 'MONTHLY') {
            $planEndDate = Carbon::now()->addMonth();
        } else {
            if ($transaction->frequency == 'YEARLY') {
                $planEndDate = Carbon::now()->addYear();
            }
        }

        /* Delete old membership */
        Upgrade::where('user_id', $user->id)->delete();

        /* Create new membership */
        Upgrade::create([
            'sub_id' => $transaction->product_id,
            'user_id' => $user->id,
            'interval' => $transaction->frequency,
            'upgrade_lasttime' => Carbon::now()->timestamp,
            'upgrade_expires' => $planEndDate->timestamp,
            'pay_mode' => 'recurring',
            'unique_id' => $gateway.'###'.$subscription_id,
            'status' => Upgrade::STATUS_ACTIVE,
        ]);

        /* Update user group */
        $user->update([
            'group_id' => $transaction->product_id,
        ]);

        return response()->json([
            'message' => 'successful',
            'status' => 200
        ], 200);
    }

}

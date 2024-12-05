<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class IyzicoController extends Controller
{

    /**
     * Process the payment
     *
     * @param  Transaction  $transaction
     */
    public static function pay(Transaction $transaction)
    {

        if ($transaction->transaction_method == 'order') {
            /* Order payment */

            $restaurant_id = $transaction->user_id;
            $post = Post::find($restaurant_id);
            $postOptions = post_options($post->id);

            $currency = @$postOptions->currency_code;

            $title = "Payment for ".$transaction->product_name;

            $user_id = $restaurant_id;
            $user_email = $post->user->email;
            $billing_name = $transaction->details->customer_name;
            $name = explode(' ', $transaction->details->customer_name, 2);
            $buyer_fname = $name[1] ?? '';
            $buyer_lname = $name[1] ?? '';

            $locationInfo = user_ip_info();

            $buyer_address = $locationInfo->location->city;
            $buyer_city = $locationInfo->location->city;
            $buyer_country = $locationInfo->location->country;
            $buyer_zipcode = $locationInfo->location->postal_code;

            $return_url = route('ipn', 'iyzico').'?order_id='.$transaction->id;

            /* Update credentials */
            config(['settings.iyzico_sandbox_mode' => @$postOptions->restaurant_iyzico_sandbox_mode]);
            config(['settings.iyzico_api_key' => @$postOptions->restaurant_iyzico_api_key]);
            config(['settings.iyzico_secret_key' => @$postOptions->restaurant_iyzico_secret_key]);

        } else {
            /* Membership Payment */

            $currency = config('settings.currency_code');
            $title = "Payment for ".$transaction->product_name." Plan (".$transaction->frequency.')';

            $user = request()->user();

            $user_id = $user->id;
            $user_email = $user->email;
            $billing_name = user_options($user->id, 'billing_name');
            $name = explode(' ', user_options($user->id, 'billing_name'), 2);
            $buyer_fname = $name[1] ?? $user->name;
            $buyer_lname = $name[1] ?? $user->name;

            $buyer_address = user_options($user->id, 'billing_address');
            $buyer_city = user_options($user->id, 'billing_city');
            $buyer_country = user_options($user->id, 'billing_country');
            $buyer_zipcode = user_options($user->id, 'billing_zipcode');

            $return_url = route('ipn', 'iyzico');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
        $request->setLocale(get_lang() == 'TR' ? \Iyzipay\Model\Locale::TR : \Iyzipay\Model\Locale::EN);
        $request->setConversationId(strval($transaction->id));
        $request->setPrice($price);
        $request->setPaidPrice($price);
        $request->setCurrency($currency);
        $request->setBasketId(strval($transaction->id));
        $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);
        $request->setCallbackUrl($return_url);

        $buyer = new \Iyzipay\Model\Buyer();
        $buyer->setId($user_id);
        $buyer->setName($buyer_fname);
        $buyer->setSurname($buyer_lname);
        $buyer->setEmail($user_email);
        $buyer->setIdentityNumber($user_id);
        $buyer->setIp(request()->ip());
        $buyer->setRegistrationAddress($buyer_address);
        $buyer->setCity($buyer_city);
        $buyer->setCountry($buyer_country);
        $buyer->setZipCode($buyer_zipcode);

        $request->setBuyer($buyer);

        $shippingAddress = new \Iyzipay\Model\Address();
        $shippingAddress->setContactName($billing_name);
        $shippingAddress->setAddress($buyer_address);
        $shippingAddress->setCity($buyer_city);
        $shippingAddress->setCountry($buyer_country);
        $shippingAddress->setZipCode($buyer_zipcode);

        $request->setShippingAddress($shippingAddress);
        $request->setBillingAddress($shippingAddress);

        $basketItems = array();
        $firstBasketItem = new \Iyzipay\Model\BasketItem();
        $firstBasketItem->setId($transaction->id);
        $firstBasketItem->setName($title);
        $firstBasketItem->setCategory1("Collectibles");
        $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::VIRTUAL);
        $firstBasketItem->setPrice($price);
        $basketItems[0] = $firstBasketItem;

        $request->setBasketItems($basketItems);

        $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, static::options());

        if ($checkoutFormInitialize->getStatus() == 'success') {

            /* redirect to payment gateway page */
            return redirect($checkoutFormInitialize->getPaymentPageUrl());
        } else {
            Log::info($checkoutFormInitialize->getErrorMessage());
            quick_alert_error($checkoutFormInitialize->getErrorMessage());
            return back()->withInput();
        }

    }

    /**
     * Handle the IPN
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public static function ipn(Request $request)
    {
        try {
            if ($request->get('token')) {

                if($request->has('order_id')) {
                    /* Order payment */

                    $transaction = Transaction::find($request->get('order_id'));
                    if (is_null($transaction)) {
                        quick_alert_error(___('Invalid transaction, please try again.'));
                        return back();
                    }

                    if ($transaction->transaction_method == 'order') {
                        $restaurant_id = $transaction->user_id;

                        config([
                            'settings.iyzico_sandbox_mode' => post_options($restaurant_id,
                                'restaurant_iyzico_sandbox_mode')
                        ]);
                        config([
                            'settings.iyzico_api_key' => post_options($restaurant_id, 'restaurant_iyzico_api_key')
                        ]);
                        config([
                            'settings.iyzico_secret_key' => post_options($restaurant_id, 'restaurant_iyzico_secret_key')
                        ]);
                    }
                }

                $form_request = new \Iyzipay\Request\RetrieveCheckoutFormRequest();
                $form_request->setLocale(get_lang() == 'TR' ? \Iyzipay\Model\Locale::TR : \Iyzipay\Model\Locale::EN);
                $form_request->setToken($request->get('token'));
                $checkoutForm = \Iyzipay\Model\CheckoutForm::retrieve($form_request, static::options());

                if ($checkoutForm->getStatus() == 'success') {

                    $transaction = Transaction::where([
                        ['id', $checkoutForm->getBasketId()],
                        ['status', null],
                        ['payment_id', null],
                    ])->first();

                    if (is_null($transaction)) {
                        quick_alert_error(___('Invalid transaction, please try again.'));
                        return redirect()->route('subscription');
                    }

                    $update = $transaction->update([
                        'transaction_gatway' => 'iyzico',
                        'payment_id' => $checkoutForm->getPaymentId(),
                        'status' => Transaction::STATUS_SUCCESS,
                    ]);

                    if ($update) {
                        if ($transaction->transaction_method == 'order') {
                            /* Order payment */
                            PaymentController::paySuccess($transaction);
                            return;
                        } else {
                            /* Membership Payment */
                            CheckoutController::updateUserPlan($transaction);
                            quick_alert_success(___('Payment successful'));
                        }
                    }
                } else {
                    quick_alert_error(___('Payment failed, please try again.'));
                    return back();
                }
            } else {
                quick_alert_error(___('Payment failed, please try again.'));
                return back();
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            quick_alert_error(___('Payment failed, please try again.'));
            return back();
        }

        return redirect()->route('subscription');
    }

    /**
     * @return \Iyzipay\Options
     */
    public static function options()
    {
        $iyzico_sandbox_mode = config('settings.iyzico_sandbox_mode');
        $iyzico_api_key = config('settings.iyzico_api_key');
        $iyzico_secret_key = config('settings.iyzico_secret_key');

        if ($iyzico_sandbox_mode == 'test') {
            $payment_link = 'https://sandbox-api.iyzipay.com';
        } else {
            $payment_link = 'https://api.iyzipay.com';
        }

        $options = new \Iyzipay\Options();
        $options->setApiKey($iyzico_api_key);
        $options->setSecretKey($iyzico_secret_key);
        $options->setBaseUrl($payment_link);

        return $options;
    }
}

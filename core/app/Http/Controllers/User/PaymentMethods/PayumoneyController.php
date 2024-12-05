<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayumoneyController extends Controller
{

    public static function configure()
    {
        \OpenPayU_Configuration::setEnvironment(config('settings.payumoney_sandbox_mode') ? 'sandbox' : 'secure');
        \OpenPayU_Configuration::setMerchantPosId(config('settings.payumoney_merchant_pos_id'));
        \OpenPayU_Configuration::setSignatureKey(config('settings.payumoney_signature_key'));
        \OpenPayU_Configuration::setOauthClientId(config('settings.payumoney_oauth_client_id'));
        \OpenPayU_Configuration::setOauthClientSecret(config('settings.payumoney_oauth_client_secret'));
        \OpenPayU_Configuration::setOauthTokenCache(new \OauthCacheFile(storage_path('framework/cache')));
    }

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

            $title = "Payment for ".$transaction->product_name;

            $currency = @$postOptions->currency_code;
            $user_name = $transaction->details->customer_name;
            $user_email = $post->user->email;

            /* Update credentials */
            config(['settings.payumoney_sandbox_mode' => @$postOptions->restaurant_payumoney_sandbox_mode]);
            config(['settings.payumoney_merchant_pos_id' => @$postOptions->restaurant_payumoney_merchant_pos_id]);
            config(['settings.payumoney_signature_key' => @$postOptions->restaurant_payumoney_signature_key]);
            config(['settings.payumoney_oauth_client_id' => @$postOptions->restaurant_payumoney_oauth_client_id]);
            config(['settings.payumoney_oauth_client_secret' => @$postOptions->restaurant_payumoney_oauth_client_secret]);

            $webhook_url = url('webhook/payumoney').'?order_id='.$transaction->id;
            $return_url = route('ipn', 'payumoney').'?order_id='.$transaction->id;
        } else {
            /* Membership Payment */

            $title = "Payment for ".$transaction->product_name." Plan (".$transaction->frequency.')';

            $currency = config('settings.currency_code');

            $user = request()->user();
            $user_name = $user->name;
            $user_email = $user->email;

            $webhook_url = url('webhook/payumoney');
            $return_url = route('ipn', 'payumoney');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        try {
            static::configure();

            $order = [
                'notifyUrl' => $webhook_url,
                'continueUrl' => $return_url,
                'customerIp' => request()->ip(),
                'merchantPosId' => \OpenPayU_Configuration::getOauthClientId() ?: \OpenPayU_Configuration::getMerchantPosId(),

                'description' => $title,
                'currencyCode' => $currency,
                'totalAmount' => $price * 100,
                'extOrderId' => $transaction->id,

                'products' => [
                    [
                        'name' => $title,
                        'unitPrice' => $price * 100,
                        'quantity' => 1
                    ]
                ],

                'buyer' => [
                    'email' => $user_email,
                    'firstName' => $user_name,
                ]
            ];

            $response = \OpenPayU_Order::create($order);
            $status_description = \OpenPayU_Util::statusDesc($response->getStatus());

            if ($response->getStatus() != 'SUCCESS') {
                Log::info($status_description);
                quick_alert_error($status_description);
                return back()->withInput();
            }

            $transaction->update(['payment_id' => $response->getResponse()->orderId]);

            /* redirect to payment gateway page */
            return redirect($response->getResponse()->redirectUri);

        } catch (\OpenPayU_Exception $e) {
            Log::info($e->getMessage());
            quick_alert_error($e->getMessage());
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
        if ($request->has('order_id')) {
            /* Order payment */

            $transaction = Transaction::find($request->get('order_id'));
            if (is_null($transaction)) {
                quick_alert_error(___('Invalid transaction, please try again.'));
                return back();
            }

            if ($transaction->transaction_method == 'order') {
                $restaurant_id = $transaction->user_id;

                $restaurant = Post::find($restaurant_id);
                ?>
                <script>
                    <?php if(!empty($transaction->details->whatsapp_url)){ ?>
                    window.open("<?php echo $transaction->details->whatsapp_url ?>", "_blank");
                    <?php } ?>

                    location.href = '<?php echo route('publicView', $restaurant->slug).'?return=success' ?>';

                </script>
                <?php

                return;
            }
        }

        /* Send a success message for recurring payment */
        quick_alert_success(___('Your payment is processing.'));

        return redirect()->route('subscription');
    }

    /**
     * Handle the Webhook
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function webhook(Request $request)
    {
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            return response()->json([
                'status' => 400
            ], 400);
        }

        /* Verify the source of the webhook event */
        $payload = $request->getContent();

        if (empty($payload)) {
            return response()->json([
                'status' => 400
            ], 400);
        }

        try {
            if ($request->has('order_id')) {
                /* Order payment */

                $transaction = Transaction::find($request->get('order_id'));
                if (is_null($transaction)) {
                    quick_alert_error(___('Invalid transaction, please try again.'));
                    return response()->json([
                        'status' => 400
                    ], 400);
                }

                if ($transaction->transaction_method == 'order') {
                    $restaurant_id = $transaction->user_id;

                    config([
                        'settings.payumoney_sandbox_mode' => post_options($restaurant_id,
                            'restaurant_payumoney_sandbox_mode')
                    ]);
                    config([
                        'settings.payumoney_merchant_pos_id' => post_options($restaurant_id,
                            'restaurant_payumoney_merchant_pos_id')
                    ]);
                    config([
                        'settings.payumoney_signature_key' => post_options($restaurant_id,
                            'restaurant_payumoney_signature_key')
                    ]);
                    config([
                        'settings.payumoney_oauth_client_id' => post_options($restaurant_id,
                            'restaurant_payumoney_oauth_client_id')
                    ]);
                    config([
                        'settings.payumoney_oauth_client_secret' => post_options($restaurant_id,
                            'restaurant_payumoney_oauth_client_secret')
                    ]);
                }
            }

            static::configure();

            $result = \OpenPayU_Order::consumeNotification($payload);

            if ($result->getResponse()->order->orderId) {

                /* Check if OrderId exists in Merchant Service, update Order data by OrderRetrieveRequest */
                $order = \OpenPayU_Order::retrieve($result->getResponse()->order->orderId);

                if ($order->getStatus() == 'SUCCESS') {

                    /* details about the payment */
                    $transaction_id = $result->getResponse()->order->extOrderId;

                    $transaction = Transaction::where([
                        ['id', $transaction_id],
                        ['status', null],
                        ['payment_id', '!=', null],
                    ])->first();

                    if ($transaction) {
                        $update = $transaction->update([
                            'transaction_gatway' => 'payumoney',
                            'status' => Transaction::STATUS_SUCCESS,
                        ]);
                        if ($update) {
                            if ($transaction->transaction_method == 'order') {
                                /* Order payment */
                                PaymentController::paySuccess($transaction);
                            } else {
                                /* Membership Payment */
                                CheckoutController::updateUserPlan($transaction);
                            }
                            return response()->json([
                                'message' => 'successful',
                                'status' => 200
                            ], 200);
                        }
                    }
                }
            }


        } catch (\OpenPayU_Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }

        return response()->json([
            'status' => 400
        ], 400);
    }
}

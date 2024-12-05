<?php

namespace App\Http\Controllers\User\PaymentMethods;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\PaymentController;
use App\Models\Post;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class MidtransController extends Controller
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

            $title = "Payment for ".$transaction->product_name;

            $mt_client_key =  @$postOptions->midtrans_client_key;
            $mt_server_key =  @$postOptions->midtrans_server_key;
            $mt_payment_mode =  @$postOptions->midtrans_sandbox_mode;

            $webhook_url = url('webhook/midtrans').'?order_id='.$transaction->id;
            $return_url = route('ipn', 'midtrans').'?order_id='.$transaction->id;
            $cancel_url = route('publicView', $post->slug);

            $color = $post->color;

            /* Update currency */
            config(['settings.currency_sign' => @$postOptions->currency_sign]);
            config(['settings.currency_pos' => @$postOptions->currency_pos]);
            config(['settings.currency_code' => @$postOptions->currency_code]);
        } else {
            /* Membership Payment */

            $title = "Payment for ".$transaction->product_name." Plan (".$transaction->frequency.')';

            $mt_client_key = config('settings.midtrans_client_key');
            $mt_server_key = config('settings.midtrans_server_key');
            $mt_payment_mode = config('settings.midtrans_sandbox_mode');

            $webhook_url = url('webhook/midtrans');
            $return_url = route('ipn', 'midtrans');
            $cancel_url = route('subscription');

            $color = config('settings.theme_color');
        }

        $price = number_format((float) $transaction->amount, 2, '.', '');

        Config::$serverKey = $mt_server_key;
        Config::$isSanitized = Config::$is3ds = true;
        Config::$overrideNotifUrl = $webhook_url;

        if ($mt_payment_mode == 'test') {
            $url = 'https://app.sandbox.midtrans.com/snap/snap.js';
        } else {
            $url = 'https://app.midtrans.com/snap/snap.js';
            Config::$isProduction = true;
        }

        $data = [
            'transaction_details' => [
                'order_id' => $transaction->id,
                'gross_amount' => $price, // no decimal allowed for creditcard
            ],
            'item_details' => [
                [
                    'id' => $transaction->product_id,
                    'price' => $price,
                    'quantity' => 1,
                    'name' => $title
                ],
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($data);

            /* display payment gateway form */
            return view(active_theme()."user.gateways.midtrans",
                compact('url', 'mt_client_key', 'snapToken', 'transaction', 'return_url','cancel_url', 'color'));

        } catch (\Exception $e) {
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function webhook(Request $request)
    {
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

                $mt_client_key = post_options($restaurant_id,
                    'midtrans_client_key');
                $mt_server_key = post_options($restaurant_id,
                    'midtrans_server_key');
                $mt_payment_mode = post_options($restaurant_id,
                    'midtrans_sandbox_mode');
            }
        } else {
            $mt_client_key = config('settings.midtrans_client_key');
            $mt_server_key = config('settings.midtrans_server_key');
            $mt_payment_mode = config('settings.midtrans_sandbox_mode');
        }

        if ($mt_payment_mode != 'test') {
            Config::$isProduction = true;
        }

        //Set Your server key
        Config::$serverKey = $mt_server_key;

        try {
            $notif = new Notification();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }

        $notif = $notif->getResponse();
        $transaction_status = $notif->transaction_status;
        $type = $notif->payment_type;
        $transaction_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $success = false;

        if ($transaction_status == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud != 'challenge') {
                    $success = true;
                }
            }
        } else if ($transaction_status == 'settlement') {
            $success = true;
        } else if ($transaction_status == 'pending') {
            $success = true;
        }

        if($success) {
            $transaction = Transaction::find($transaction_id);

            if($transaction) {
                $update = $transaction->update([
                    'transaction_gatway' => 'midtrans',
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

        return response()->json([
            'status' => 400
        ], 400);
    }
}

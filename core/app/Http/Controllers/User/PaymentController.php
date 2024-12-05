<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Post;
use App\Models\PostOption;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Validator;

class PaymentController extends Controller
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
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Transaction $transaction)
    {
        $restaurant_id = $transaction->user_id;
        $post = Post::find($restaurant_id);
        if ($post && $post->user->status == 1) {

            /* check user's plan is enabled */
            $plan = $post->user->plan();
            if ($plan->status) {

                $postOptions = post_options($post->id);
                $theme = $postOptions->restaurant_template;

                /* Update currency */
                config(['settings.currency_sign' => @$postOptions->currency_sign]);
                config(['settings.currency_pos' => @$postOptions->currency_pos]);
                config(['settings.currency_code' => @$postOptions->currency_code]);

                $paymentGateways = [];

                foreach ([
                             'paypal', 'stripe', 'razorpay', 'mollie', 'paytm', 'paystack', 'payumoney', 'iyzico', 'midtrans', 'paytabs', 'telr', 'ccavenue'
                         ] as $gateway) {
                    if (@$postOptions->{'restaurant_'.$gateway.'_install'}) {
                        $paymentGateways[] = (object) [
                            'id' => $gateway,
                            'payment_folder' => $gateway,
                            'payment_title' => $postOptions->{'restaurant_'.$gateway.'_title'} ?? ucfirst($gateway),
                        ];
                    }
                }
                /* 2checkout */
                if (@$postOptions->restaurant_2checkout_install) {
                    $paymentGateways[] = (object) [
                        'id' => 'two_checkout',
                        'payment_folder' => 'two_checkout',
                        'payment_title' => @$postOptions->restaurant_2checkout_title ?? '2Checkout'
                    ];
                }

                return view('post_templates.payment', compact(
                    'transaction',
                    'post',
                    'postOptions',
                    'theme',
                    'plan',
                    'paymentGateways'
                ));
            }
        }

        abort(404);
    }

    /**
     * Handle Payment
     *
     * @param  Request  $request
     * @param  Transaction  $transaction
     */
    public function pay(Request $request, Transaction $transaction)
    {
        $restaurant_id = $transaction->user_id;
        $post = Post::find($restaurant_id);
        if ($post && $post->user->status == 1) {

            /* check user's plan is enabled */
            $plan = $post->user->plan();
            if ($plan->status) {

                $paymentController = __NAMESPACE__.'\PaymentMethods\\'
                    .str_replace(
                        ' ', '',
                        ucwords(
                            str_replace('_', ' ', $request->get('payment_method')))
                    )
                    .'Controller';
                return $paymentController::pay($transaction);
            }
        }

        abort(404);
    }

    /**
     * After payment success
     *
     * @param  Transaction  $transaction
     */
    public static function paySuccess(Transaction $transaction)
    {
        /* mark order as paid */
        Order::find($transaction->product_id)
            ->update([
                'is_paid' => 1,
                'payment_gateway' => $transaction->transaction_gatway,
                'status' => 'pending'
            ]);

        $restaurant_id = $transaction->user_id;
        $wallet_amount = post_options($restaurant_id, 'wallet_amount', 0);
        $wallet_amount += $transaction->amount;
        PostOption::updatePostOption($restaurant_id, 'wallet_amount', $wallet_amount);

        $restaurant = Post::find($restaurant_id);
        ?>
        <script>
            <?php if(!empty($transaction->details->whatsapp_url)){ ?>
            window.open("<?php echo $transaction->details->whatsapp_url ?>", "_blank");
            <?php } ?>

            location.href = '<?php echo route('publicView', $restaurant->slug).'?return=success' ?>';

        </script>
        <?php

        /* Delete processed transaction */
        $transaction->delete();
    }
}

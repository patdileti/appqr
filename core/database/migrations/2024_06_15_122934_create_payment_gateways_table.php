<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('payment_gateways')) {
            Schema::create('payment_gateways', function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->enum('payment_install', ['0', '1'])->default('0');
                $table->string('payment_title')->nullable();
                $table->string('payment_folder', 30)->nullable();
            });

            $payment_gateways = [
                ['id' => '1', 'payment_install' => '0', 'payment_title' => 'Paypal', 'payment_folder' => 'paypal'],
                [
                    'id' => '2', 'payment_install' => '0', 'payment_title' => 'Credit & Debit Card',
                    'payment_folder' => 'stripe'
                ],
                [
                    'id' => '3', 'payment_install' => '1', 'payment_title' => 'Bank Deposit (Offline Payment)',
                    'payment_folder' => 'wire_transfer'
                ],
                [
                    'id' => '4', 'payment_install' => '0', 'payment_title' => '2Checkout',
                    'payment_folder' => 'two_checkout'
                ],
                ['id' => '5', 'payment_install' => '0', 'payment_title' => 'Paystack', 'payment_folder' => 'paystack'],
                [
                    'id' => '6', 'payment_install' => '0', 'payment_title' => 'Payumoney',
                    'payment_folder' => 'payumoney'
                ],
                ['id' => '7', 'payment_install' => '0', 'payment_title' => 'Paytm', 'payment_folder' => 'paytm'],
                ['id' => '8', 'payment_install' => '0', 'payment_title' => 'CCAvenue', 'payment_folder' => 'ccavenue'],
                ['id' => '9', 'payment_install' => '0', 'payment_title' => 'Mollie', 'payment_folder' => 'mollie'],
                ['id' => '10', 'payment_install' => '0', 'payment_title' => 'Iyzico', 'payment_folder' => 'iyzico'],
                ['id' => '11', 'payment_install' => '0', 'payment_title' => 'Midtrans', 'payment_folder' => 'midtrans'],
                ['id' => '12', 'payment_install' => '0', 'payment_title' => 'PayTabs', 'payment_folder' => 'paytabs'],
                ['id' => '13', 'payment_install' => '0', 'payment_title' => 'Telr', 'payment_folder' => 'telr'],
                ['id' => '14', 'payment_install' => '0', 'payment_title' => 'Razorpay', 'payment_folder' => 'razorpay'],
                ['id' => '15', 'payment_install' => '0', 'payment_title' => 'Paddle', 'payment_folder' => 'paddle']
            ];

            DB::table('payment_gateways')->insert($payment_gateways);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_gateways');
    }
};

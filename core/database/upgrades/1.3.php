<?php
/* Version 1.3 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/* Add new payment gateway */
$gateways = [
    ['payment_install' => '0', 'payment_title' => 'Paddle', 'payment_folder' => 'paddle']
];
DB::table('payment_gateways')->insert($gateways);

/* Increase length of transaction_ip */
Schema::table('transaction', function (Blueprint $table) {
    $table->string('transaction_ip', 100)->nullable()->change();
});

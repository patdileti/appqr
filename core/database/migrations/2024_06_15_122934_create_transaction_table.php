<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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
        if(!Schema::hasTable('transaction')) {
            Schema::create('transaction', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('product_name', 225)->nullable();
                $table->integer('product_id')->nullable();
                $table->integer('user_id')->nullable()->index('user_id');
                $table->enum('status', ['pending', 'success', 'failed', 'cancel'])->nullable();
                $table->double('amount')->nullable();
                $table->double('base_amount')->nullable();
                $table->string('currency_code', 3)->nullable();
                $table->string('payment_id')->nullable();
                $table->string('transaction_gatway')->nullable();
                $table->string('transaction_method', 20)->nullable();
                $table->string('transaction_description')->nullable();
                $table->string('transaction_ip', 100)->nullable();
                $table->enum('frequency', ['MONTHLY', 'YEARLY', 'LIFETIME'])->nullable();
                $table->text('billing')->nullable();
                $table->text('taxes_ids')->nullable();
                $table->text('details')->nullable();
                $table->text('coupon')->nullable();
                $table->enum('featured', ['0', '1'])->nullable()->default('0');
                $table->enum('urgent', ['0', '1'])->nullable()->default('0');
                $table->enum('highlight', ['0', '1'])->nullable()->default('0');
                $table->timestamps();

                $table->foreign('user_id')->references(['id'])->on('user')->onDelete('CASCADE');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
};

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
        if(!Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('restaurant_id')->nullable()->index('restaurant_id');
                $table->enum('type', ['on-table', 'takeaway', 'delivery'])->default('on-table');
                $table->string('customer_name')->nullable();
                $table->integer('table_number')->nullable();
                $table->string('phone_number', 25)->nullable();
                $table->string('address')->nullable();
                $table->enum('status', ['pending', 'completed', 'unpaid'])->default('pending');
                $table->string('message')->nullable();
                $table->boolean('seen')->default(false);
                $table->boolean('is_paid')->default(false);
                $table->string('payment_gateway', 25)->nullable();
                $table->dateTime('created_at')->nullable();

                $table->foreign('restaurant_id')->references(['id'])->on('posts')->onDelete('CASCADE');
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
        Schema::dropIfExists('orders');
    }
};

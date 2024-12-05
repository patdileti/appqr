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
        if(!Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_id')->nullable()->index('order_id');
                $table->integer('item_id')->nullable()->index('item_id');
                $table->integer('variation')->nullable();
                $table->integer('quantity')->default(1);

                $table->foreign('order_id')->references(['id'])->on('orders')->onDelete('CASCADE');
                $table->foreign('item_id')->references(['id'])->on('menu')->onDelete('CASCADE');
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
        Schema::dropIfExists('order_items');
    }
};

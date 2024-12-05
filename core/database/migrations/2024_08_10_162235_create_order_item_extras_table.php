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
        if(!Schema::hasTable('order_item_extras')) {
            Schema::create('order_item_extras', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('order_item_id')->nullable()->index('order_item_id');
                $table->integer('extra_id')->nullable()->index('extra_id');
                $table->integer('quantity')->default(1);

                $table->foreign('order_item_id')->references(['id'])->on('order_items')->onDelete('CASCADE');
                $table->foreign('extra_id')->references(['id'])->on('menu_extras')->onDelete('CASCADE');
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
        Schema::dropIfExists('order_item_extras');
    }
};

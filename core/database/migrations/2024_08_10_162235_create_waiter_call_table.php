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
        if(!Schema::hasTable('waiter_call')) {
            Schema::create('waiter_call', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('restaurant_id')->nullable()->index('restaurant_id');
                $table->integer('table_no')->nullable();
                $table->boolean('seen')->default(false);

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
        Schema::dropIfExists('waiter_call');
    }
};

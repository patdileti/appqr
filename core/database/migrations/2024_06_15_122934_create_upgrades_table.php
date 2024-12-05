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
        if(!Schema::hasTable('upgrades')) {
            Schema::create('upgrades', function (Blueprint $table) {
                $table->increments('upgrade_id');
                $table->string('sub_id', 16)->default('0');
                $table->integer('user_id')->default(0)->index('user_id');
                $table->enum('pay_mode', ['one_time', 'recurring'])->default('one_time');
                $table->enum('interval', ['MONTHLY', 'YEARLY', 'LIFETIME'])->nullable();
                $table->unsignedBigInteger('upgrade_lasttime')->default(0);
                $table->unsignedBigInteger('upgrade_expires')->default(0);
                $table->string('unique_id')->nullable();
                $table->string('status')->nullable();

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
        Schema::dropIfExists('upgrades');
    }
};

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
        if(!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->longText('translations')->nullable();
                $table->double('monthly_price', 8, 2)->default(0);
                $table->double('annual_price', 8, 2)->default(0);
                $table->double('lifetime_price', 8, 2)->default(0);
                $table->text('settings');
                $table->enum('recommended', ['yes', 'no'])->default('no');
                $table->text('taxes_ids')->nullable();
                $table->integer('position')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
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
        Schema::dropIfExists('plans');
    }
};

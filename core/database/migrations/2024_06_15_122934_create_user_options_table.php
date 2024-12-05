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
        if(!Schema::hasTable('user_options')) {
            Schema::create('user_options', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id')->nullable()->index('user_id');
                $table->string('option_name', 191)->nullable();
                $table->longText('option_value')->nullable();

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
        Schema::dropIfExists('user_options');
    }
};

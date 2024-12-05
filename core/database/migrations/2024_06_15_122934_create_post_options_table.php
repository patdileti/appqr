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
        if(!Schema::hasTable('post_options')) {
            Schema::create('post_options', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('post_id')->nullable()->index('post_id');
                $table->string('option_name', 191)->nullable();
                $table->longText('option_value')->nullable();

                $table->foreign('post_id')->references(['id'])->on('posts')->onDelete('CASCADE');
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
        Schema::dropIfExists('post_options');
    }
};

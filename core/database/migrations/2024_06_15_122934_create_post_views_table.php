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
        if(!Schema::hasTable('post_views')) {
            Schema::create('post_views', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('post_id')->nullable()->index('post_id');
                $table->string('ip')->nullable();
                $table->dateTime('date')->nullable();

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
        Schema::dropIfExists('post_views');
    }
};

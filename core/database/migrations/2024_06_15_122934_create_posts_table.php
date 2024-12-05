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
        if(!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id')->nullable()->index('user_id');
                $table->string('slug')->nullable();
                $table->string('color', 10)->nullable();
                $table->string('title');
                $table->string('sub_title')->nullable();
                $table->text('description')->nullable();
                $table->string('main_image')->nullable()->default('default.png');
                $table->string('cover_image')->nullable()->default('default.png');
                $table->string('timing')->nullable();
                $table->string('address')->nullable();
                $table->string('phone', 20)->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

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
        Schema::dropIfExists('posts');
    }
};

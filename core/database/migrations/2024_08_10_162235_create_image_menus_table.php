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
        if(!Schema::hasTable('image_menus')) {
            Schema::create('image_menus', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('restaurant_id')->nullable()->index('restaurant_id');
                $table->string('name')->nullable();
                $table->string('image')->nullable();
                $table->enum('active', ['0', '1'])->default('1');
                $table->integer('position')->default(9999);

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
        Schema::dropIfExists('image_menus');
    }
};

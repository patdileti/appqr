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
        if(!Schema::hasTable('menu')) {
            Schema::create('menu', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('category_id')->nullable()->index('category_id');
                $table->integer('restaurant_id')->nullable()->index('restraurant_id');
                $table->string('name')->nullable();
                $table->string('description')->nullable();
                $table->float('price', 10, 0)->nullable()->default(0);
                $table->string('image')->nullable()->default('default.png');
                $table->enum('type', ['veg', 'nonveg'])->default('veg');
                $table->text('allergies')->nullable();
                $table->enum('active', ['0', '1'])->default('1');
                $table->integer('position')->default(9999);
                $table->longText('translations')->nullable();

                $table->foreign('category_id')->references(['id'])->on('menu_categories')->onDelete('CASCADE');
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
        Schema::dropIfExists('menu');
    }
};

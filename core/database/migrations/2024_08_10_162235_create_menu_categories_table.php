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
        if(!Schema::hasTable('menu_categories')) {
            Schema::create('menu_categories', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('restaurant_id')->nullable()->index('restaurant_id');
                $table->string('name')->nullable();
                $table->integer('parent')->nullable()->index('parent');
                $table->integer('position')->nullable()->default(999);
                $table->longText('translations')->nullable();

                $table->foreign('restaurant_id')->references(['id'])->on('posts')->onDelete('CASCADE');
                $table->foreign('parent')->references(['id'])->on('menu_categories')->onUpdate('SET NULL')->onDelete('CASCADE');
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
        Schema::dropIfExists('menu_categories');
    }
};

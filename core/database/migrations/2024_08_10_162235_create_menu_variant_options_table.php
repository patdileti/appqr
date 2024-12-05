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
        if(!Schema::hasTable('menu_variant_options')) {
            Schema::create('menu_variant_options', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('menu_id')->nullable()->index('menu_id');
                $table->string('title')->nullable();
                $table->text('options')->nullable();
                $table->integer('position')->default(9999);
                $table->boolean('active')->default(true);
                $table->longText('translations')->nullable();

                $table->foreign('menu_id')->references(['id'])->on('menu')->onDelete('CASCADE');
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
        Schema::dropIfExists('menu_variant_options');
    }
};

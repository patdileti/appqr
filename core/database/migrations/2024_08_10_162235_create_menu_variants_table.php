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
        if(!Schema::hasTable('menu_variants')) {
            Schema::create('menu_variants', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('menu_id')->nullable()->index('menu_id');
                $table->float('price', 10, 0)->nullable();
                $table->longText('options')->nullable();
                $table->boolean('active')->default(true);
                $table->integer('position')->default(9999);

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
        Schema::dropIfExists('menu_variants');
    }
};

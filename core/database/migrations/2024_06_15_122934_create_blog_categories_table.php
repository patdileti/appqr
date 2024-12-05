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
        if(!Schema::hasTable('blog_categories')) {

            Schema::create('blog_categories', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('title', 50)->nullable();
                $table->string('slug', 50)->nullable();
                $table->integer('position')->default(0);
                $table->enum('active', ['0', '1'])->default('1');
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
        Schema::dropIfExists('blog_categories');
    }
};

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
        if(!Schema::hasTable('blog_cat_relation')) {

            Schema::create('blog_cat_relation', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('blog_id')->nullable()->index('blog_id');
                $table->integer('category_id')->nullable()->index('category_id');

                $table->foreign('blog_id')->references(['id'])->on('blog')->onDelete('CASCADE');
                $table->foreign('category_id')->references(['id'])->on('blog_categories')->onDelete('CASCADE');
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
        Schema::dropIfExists('blog_cat_relation');
    }
};

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
        if(!Schema::hasTable('blog')) {
            Schema::create('blog', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('author')->nullable()->index('author');
                $table->string('title')->nullable();
                $table->string('slug')->nullable();
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->text('tags')->nullable();
                $table->enum('status', ['publish', 'pending'])->default('publish');
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

                $table->foreign('author')->references(['id'])->on('user')->onDelete('CASCADE');
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
        Schema::dropIfExists('blog');
    }
};

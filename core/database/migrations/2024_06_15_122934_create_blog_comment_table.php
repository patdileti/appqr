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
        if(!Schema::hasTable('blog_comment')) {

            Schema::create('blog_comment', function (Blueprint $table) {
                $table->integer('id', true);
                $table->integer('user_id')->nullable()->index('user_id');
                $table->integer('blog_id')->nullable()->index('blog_id');
                $table->tinyText('name')->nullable();
                $table->string('email', 100)->nullable();
                $table->text('comment');
                $table->integer('parent')->default(0);
                $table->enum('active', ['0', '1'])->default('1')->comment('0:Deactive
1:Active');
                $table->timestamps();

                $table->foreign('user_id')->references(['id'])->on('user')->onDelete('CASCADE');
                $table->foreign('blog_id')->references(['id'])->on('blog')->onDelete('CASCADE');
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
        Schema::dropIfExists('blog_comment');
    }
};

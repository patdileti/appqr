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
        if(!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('translation_lang', 10)->nullable()->index('translation_lang');
                $table->enum('type', ['0', '1'])->default('0')->comment('0:Public
1:Private');
                $table->string('slug', 100)->nullable();
                $table->string('title', 200)->nullable();
                $table->text('content')->nullable();
                $table->boolean('active')->nullable()->default(true);
                $table->timestamps();
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
        Schema::dropIfExists('pages');
    }
};

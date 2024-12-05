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
        if(!Schema::hasTable('allergies')) {
            Schema::create('allergies', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('title')->nullable();
                $table->longText('translations')->nullable();
                $table->string('image')->default('default.png');
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
        Schema::dropIfExists('allergies');
    }
};

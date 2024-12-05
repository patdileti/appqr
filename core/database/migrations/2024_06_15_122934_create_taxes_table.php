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
        if(!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('internal_name', 64)->nullable();
                $table->string('name', 64)->nullable();
                $table->string('description', 256)->nullable();
                $table->decimal('value', 10)->nullable();
                $table->enum('value_type', ['percentage', 'fixed'])->nullable();
                $table->enum('type', ['inclusive', 'exclusive'])->nullable();
                $table->enum('billing_type', ['personal', 'business', 'both'])->nullable();
                $table->text('countries')->nullable();
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
        Schema::dropIfExists('taxes');
    }
};

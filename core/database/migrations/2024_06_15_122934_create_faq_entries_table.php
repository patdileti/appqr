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
        if(!Schema::hasTable('faq_entries')) {
            Schema::create('faq_entries', function (Blueprint $table) {
                $table->mediumIncrements('id');
                $table->string('faq_title')->nullable();
                $table->mediumText('faq_content')->nullable();
                $table->string('translation_lang', 10)->nullable()->index('translation_lang');
                $table->boolean('active')->nullable()->default(true);
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
        Schema::dropIfExists('faq_entries');
    }
};

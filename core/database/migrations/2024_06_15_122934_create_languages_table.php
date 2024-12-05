<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('code', 10)->nullable();
                $table->string('direction', 3)->nullable();
                $table->string('name', 100)->nullable();
                $table->boolean('active')->default(true);
                $table->integer('position')->default(999);
            });

            $languages = [
                ['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'position' => '0', 'active' => '1'],
                ['code' => 'fr', 'name' => 'French', 'direction' => 'ltr', 'position' => '1', 'active' => '1']
            ];

            DB::table('languages')->insert($languages);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('languages');
    }
};

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
        if(!Schema::hasTable('testimonials')) {
            Schema::create('testimonials', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('designation');
                $table->string('content');
                $table->string('image');
                $table->longText('translations')->nullable();
                $table->timestamps();
            });

            $testimonials = [
                [
                    'id' => '1', 'name' => 'Natasha', 'designation' => 'Designer',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium cumque dolor ducimus ea error exercitationem hic in ipsa minima odio odit, porro quia sapiente sed similique sint totam! Mollitia, porro?',
                    'image' => 'default_user.png', 'created_at' => '2024-05-09 03:34:11',
                    'updated_at' => '2024-05-09 03:34:11'
                ],
                [
                    'id' => '2', 'name' => 'John', 'designation' => 'Writer',
                    'content' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Accusantium cumque dolor ducimus ea error exercitationem hic in ipsa minima odio odit, porro quia sapiente sed similique sint totam! Mollitia, porro?',
                    'image' => 'default_user.png', 'created_at' => '2024-05-09 03:35:48',
                    'updated_at' => '2024-05-09 03:35:48'
                ]
            ];

            DB::table('testimonials')->insert($testimonials);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        if(!Schema::hasTable('adsense')) {

            Schema::create('adsense', function (Blueprint $table) {
                $table->integer('id', true);
                $table->string('provider_name')->nullable();
                $table->text('slug')->nullable();
                $table->text('code')->nullable();
                $table->boolean('status')->default(false);
            });

            $adsense = [
                [
                    'id' => '1', 'provider_name' => 'Head Code', 'slug' => 'head_code', 'code' => '<script></script>',
                    'status' => '1'
                ],
                [
                    'id' => '2', 'provider_name' => 'Top', 'slug' => 'top',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '1'
                ],
                [
                    'id' => '3', 'provider_name' => 'Bottom', 'slug' => 'bottom',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '1'
                ],
                [
                    'id' => '4', 'provider_name' => 'Home Page 1', 'slug' => 'home_page_1',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '1'
                ],
                [
                    'id' => '5', 'provider_name' => 'Home Page 2', 'slug' => 'home_page_2',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '1'
                ],
                [
                    'id' => '12', 'provider_name' => 'Dashboard Top', 'slug' => 'dashboard_top',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '0'
                ],
                [
                    'id' => '13', 'provider_name' => 'Dashboard Bottom', 'slug' => 'dashboard_bottom',
                    'code' => '<img src="https://via.placeholder.com/720x90" width="100%" height="100%">',
                    'status' => '1'
                ]
            ];

            DB::table('adsense')->insert($adsense);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adsense');
    }
};

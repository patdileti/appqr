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
        if(!Schema::hasTable('user')) {
            Schema::create('user', function (Blueprint $table) {
                $table->integer('id', true);
                $table->enum('user_type', ['user', 'admin'])->default('user');
                $table->string('group_id', 16)->default('free');
                $table->string('username')->nullable()->unique();
                $table->string('email')->nullable()->unique();
                $table->string('name', 225)->nullable();
                $table->string('password')->nullable();
                $table->string('image', 225)->default('default_user.png');
                $table->float('balance', 10)->default(0);
                $table->char('country_code', 50)->nullable();
                $table->string('country', 50)->nullable();
                $table->enum('oauth_provider', ['', 'facebook', 'google', 'twitter'])->nullable();
                $table->string('oauth_uid', 100)->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->boolean('status')->default(true);
                $table->dateTime('lastactive')->nullable();
                $table->dateTime('created_at')->nullable();
                $table->dateTime('updated_at')->nullable();

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
        Schema::dropIfExists('user');
    }
};

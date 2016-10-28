<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->default(2);
            $table->integer('city_id')->unsigned()->nullable();
            $table->integer('media_id')->unsigned()->nullable();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('login')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password', 64);

            $table->date('bdate')->nullable();
            $table->tinyInteger('sex')->nullable();
            $table->tinyInteger('timezone')->nullable();
            $table->integer('vk_id')->nullable()->unique();

            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_enable')->default(true);
            $table->rememberToken();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}

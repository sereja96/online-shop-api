<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('media_id')->nullable();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->double('rate')->default(3);
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_enable')->default(true);
            $table->nullableTimestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop');
    }
}

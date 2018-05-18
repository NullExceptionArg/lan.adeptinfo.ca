<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableContribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lan_id')->nullable(true);
            $table->unsignedInteger('user_id');
            $table->string('user_full_name')->nullable(true);

            $table->foreign('user_id')->references('id')->on('user');
            $table->foreign('lan_id')->references('id')->on('lan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution');
    }
}

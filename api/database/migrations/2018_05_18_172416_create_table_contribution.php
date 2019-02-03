<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableContribution extends Migration
{
    /**
     * Exécuter les migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable(true);
            $table->string('user_full_name')->nullable(true);
            $table->softDeletes();

            $table->foreign('user_id')
                ->references('id')->on('user')
                ->onDelete('cascade');
        });
    }

    /**
     * Inverser les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contribution');
    }
}

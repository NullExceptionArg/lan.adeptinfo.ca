<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableTeam extends Migration
{
    /**
     * Exécuter les migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('tag');
            $table->unsignedInteger('tournament_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tournament_id')
                ->references('id')->on('tournament');
        });
    }

    /**
     * Inverser les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableOrganizerTournament extends Migration
{
    /**
     * Exécuter les migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizer_tournament', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('organizer_id');
            $table->unsignedInteger('tournament_id');
            $table->timestamps();

            $table->foreign('organizer_id')
                ->references('id')->on('user');
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
        Schema::dropIfExists('organizer_tournament');
    }
}

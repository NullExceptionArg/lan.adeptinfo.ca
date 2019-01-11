<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableOrganizerTournament extends Migration
{
    /**
     * Run the migrations.
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
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizer_tournament');
    }
}

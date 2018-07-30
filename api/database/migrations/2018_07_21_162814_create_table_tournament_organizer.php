<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTournamentOrganizer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_organizer', function (Blueprint $table) {
            $table->unsignedInteger('tournament_id');
            $table->unsignedInteger('organizer_id');

            $table->foreign('tournament_id')
                ->references('id')->on('tournament');
            $table->foreign('organizer_id')
                ->references('id')->on('user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament_organizer');
    }
}

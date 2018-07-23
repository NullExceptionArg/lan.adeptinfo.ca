<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTournament extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->dateTime('tournament_start');
            $table->dateTime('tournament_end');
            $table->unsignedInteger('players_to_reach');
            $table->unsignedInteger('teams_to_reach');
            $table->enum('state', ['hidden', 'visible' ])->default('hidden');
            $table->text('rules');
            $table->unsignedInteger('lan_id');
            $table->timestamps();

            $table->foreign('lan_id')
                ->references('id')->on('lan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournament');
    }
}

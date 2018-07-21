<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTeamPlayer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_player', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag');
            $table->boolean('is_leader');
            $table->unsignedInteger('player_id');
            $table->unsignedInteger('team_id');

            $table->foreign('player_id')
                ->references('id')->on('user');
            $table->foreign('team_id')
                ->references('id')->on('team');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('team_player');
    }
}

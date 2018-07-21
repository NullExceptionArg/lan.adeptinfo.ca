<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTournamentAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournament_admin', function (Blueprint $table) {
            $table->unsignedInteger('tournament_id');
            $table->unsignedInteger('user_id');

            $table->foreign('tournament_id')
                ->references('id')->on('tournament');
            $table->foreign('user_id')
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
        Schema::dropIfExists('tournament_admin');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableTagTeam extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_team', function (Blueprint $table) {
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('team_id');
            $table->boolean('is_leader');
            $table->timestamps();

            $table->foreign('tag_id')
                ->references('id')->on('tag');
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
        Schema::dropIfExists('tag_team');
    }
}
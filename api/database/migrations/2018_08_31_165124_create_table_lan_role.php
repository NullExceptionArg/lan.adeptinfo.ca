<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableLanRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lan_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('en_display_name');
            $table->string('en_description', 500);
            $table->string('fr_display_name');
            $table->string('fr_description', 500);
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
        Schema::dropIfExists('lan_role');
    }
}

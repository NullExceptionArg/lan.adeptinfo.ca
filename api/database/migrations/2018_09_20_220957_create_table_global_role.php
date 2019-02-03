<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableGlobalRole extends Migration
{
    /**
     * Exécuter les migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('en_display_name');
            $table->string('en_description', 500);
            $table->string('fr_display_name');
            $table->string('fr_description', 500);
            $table->timestamps();
        });
    }

    /**
     * Inverser les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_role');
    }
}

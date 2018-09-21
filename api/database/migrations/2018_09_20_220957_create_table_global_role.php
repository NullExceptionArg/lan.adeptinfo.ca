<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableGlobalRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('en_display_name');
            $table->string('en_description');
            $table->string('fr_display_name');
            $table->string('fr_description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_role');
    }
}

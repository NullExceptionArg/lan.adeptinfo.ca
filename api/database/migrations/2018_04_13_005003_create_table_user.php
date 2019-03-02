<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableUser extends Migration
{
    /**
     * Exécuter les migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user', function(Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable(false);
            $table->string('last_name')->nullable(false);
            $table->string('email')->nullable(false);
            $table->unique('email');
            $table->string('password')->nullable(true);
            $table->string('facebook_id')->nullable(true);
            $table->string('google_id')->nullable(true);
            $table->boolean('is_confirmed')->default(false);
            $table->string('confirmation_code')->nullable();
            $table->timestamps();

            $table->unique('facebook_id');
            $table->unique('google_id');
        });
    }

    /**
     * Inverser les migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user');
    }
}

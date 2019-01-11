<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableLan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lan', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->dateTime('lan_start');
            $table->dateTime('lan_end');
            $table->dateTime('seat_reservation_start');
            $table->dateTime('tournament_reservation_start');
            $table->string('event_key'); // seat.io
            $table->string('public_key'); // seat.io
            $table->string('secret_key'); // seat.io
            $table->boolean('is_current')->default(false);
            $table->unsignedInteger('places');
            $table->decimal('longitude', 10, 7);
            $table->decimal('latitude', 10, 7);
            $table->unsignedInteger('price')->default(0);
            $table->text('rules')->nullable(true);
            $table->text('description')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lan');
    }
}

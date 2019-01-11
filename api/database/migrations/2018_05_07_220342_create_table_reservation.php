<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableReservation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservation', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lan_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->string('seat_id');
            $table->dateTime('arrived_at')->nullable(true);
            $table->dateTime('left_at')->nullable(true);
            $table->timestamps(); // create_at = reservation date
            $table->softDeletes(); // deleted_at = cancellation date

            $table->foreign('user_id')
                ->references('id')->on('user')
                ->onDelete('cascade');
            $table->foreign('lan_id')
                ->references('id')->on('lan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservation');
    }
}

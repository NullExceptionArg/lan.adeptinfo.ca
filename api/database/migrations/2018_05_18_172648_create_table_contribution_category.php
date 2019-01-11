<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableContributionCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable(false);
            $table->unsignedInteger('lan_id');
            $table->softDeletes();

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
        Schema::dropIfExists('contribution_cat_contribution');
        Schema::dropIfExists('contribution_category');
    }
}

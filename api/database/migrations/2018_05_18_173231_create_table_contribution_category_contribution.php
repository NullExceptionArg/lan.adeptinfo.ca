<?php

use Illuminate\{Database\Migrations\Migration, Database\Schema\Blueprint, Support\Facades\Schema};

class CreateTableContributionCategoryContribution extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contribution_cat_contribution', function (Blueprint $table) {
            $table->unsignedInteger('contribution_category_id');
            $table->unsignedInteger('contribution_id');

            $table->foreign('contribution_category_id')
                ->references('id')->on('contribution_category')
                ->onDelete('cascade');
            $table->foreign('contribution_id')
                ->references('id')->on('contribution')
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
        Schema::dropIfExists('contributor_category_contributor');
    }
}

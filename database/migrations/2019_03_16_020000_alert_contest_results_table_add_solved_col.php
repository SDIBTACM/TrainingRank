<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlertContestResultsTableAddSolvedCol extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contest_results', function (Blueprint $table) {
            $table->integer('solved')->default(0);
            $table->integer('solved_rating')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contest_results', function (Blueprint $table) {
            $table->dropColumn('solved');
            $table->dropColumn('solved_rating');
        });
    }
}

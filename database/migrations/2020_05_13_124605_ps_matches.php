<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PsMatches extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function(Blueprint $table) {
            $table->bigInteger('id')->primary();
            $table->char('name');
            $table->dateTime('begin_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->tinyInteger('forfeit')->nullable();
            $table->bigInteger('game_advantage')->nullable();
            $table->bigInteger('league_id')->nullable();
            $table->enum('match_type', ['best_of', 'custom', 'first_to', 'ow_best_of']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}

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
            $table->bigInteger('id');
            $table->char('name');
            $table->dateTimeTz('begin_at')->nullable();
            $table->dateTimeTz('end_at')->nullable();
            $table->tinyInteger('forfeit')->nullable();
            $table->bigInteger('game_advantage')->nullable();
            $table->bigInteger('league_id')->nullable();
            if(env('DB_CONNECTION') == 'sqlite')
                $table->char('match_type');
            else
                $table->set('match_type', ["best_of", "custom", "first_to", "ow_best_of"]);
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

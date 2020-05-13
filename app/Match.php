<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Cast\DateWithTz;

class Match extends Model
{   
    protected $fillable = [
        'id',
        'name',
        'begin_at',
        'end_at',
        'forfeit',
        'game_advantage',
        'league_id',
        'match_type',
    ];

    protected $casts = [
        'begin_at' => DateWithTz::class,
        'end_at' => DateWithTz::class,
        'forfeit' => 'bool'
    ];

    public $timestamps = false;
    public $incrementing = false;

    function opponents(): array
    {
        $opponents = [];
        foreach($this->teams as $opponent)
            $opponents[] = $opponent;
        foreach($this->players as $opponent)
        {
            $opponents[] = $opponent;
        }
        return $opponents;
    }

    function teams()
    {
        return $this->morphedByMany('App\Team', 'opponents');
    }

    function players()
    {
        return $this->morphedByMany('App\Player', 'opponents');
    }
}

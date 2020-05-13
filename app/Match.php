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
}

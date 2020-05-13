<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'id',
        'name',
        'url',
    ];

    public $timestamps = false;
    public $incrementing = false;
}

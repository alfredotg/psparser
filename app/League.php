<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    protected $fillable = [
        'id',
        'name',
        'slug',
        'url',
    ];

    public $timestamps = false;
}

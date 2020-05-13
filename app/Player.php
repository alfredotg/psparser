<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'id',
        'name',
        'first_name',
        'last_name',
    ];

    public $timestamps = false;
    public $incrementing = false;
}

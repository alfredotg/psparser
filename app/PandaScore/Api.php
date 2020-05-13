<?php

namespace App\PandaScore;

use Illuminate\Support\Str;

class Api 
{
    function __construct() 
    {
        $this->base_path = config('pandascore.base_path');
        $this->token = config('pandascore.token');
    }

    /*
     * Mapping calls "mathesRunning(10)" to "matches/running/10"
     */
    function __call($function, $args)
    {                                                
        $path = Str::snake($function, '/');
        if(count($args) > 0)
            $path .= '/' . implode('/', $args);
        return $this->call($path);
    }

    function call(string $path, array $query = [])
    {
        $query['token'] = $this->token;
        return new Request(Str::finish($this->base_path, '/') . ltrim($path, '/'), $query);
    }
}

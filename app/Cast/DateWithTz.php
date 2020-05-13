<?php

namespace App\Cast;

use DateTime;
use DateTimeZone;

class DateWithTz
{
    public function get($model, $key, $value, $attributes)
    {
        return new DateTime($value);
    }

    /*
     * Caste string with timezone (like '2020-05-05T12:28:25MSK') to app timezone 
     */
    public function set($model, $key, $value, $attributes)
    {
        if(is_a($value, DateTime::class))
            $data = $value;
        else
            $data = new DateTime($value);
        $data->setTimeZone(new DateTimeZone(config('app.timezone')));
        return $data->format('Y-m-d H:i:s');
    }
}

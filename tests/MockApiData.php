<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait MockApiData
{
    public function apiDataString(string $file): string
    {
        return file_get_contents(base_path('tests/json/' . $file));
    }

    public function apiData(string $file)
    {
        return json_decode($this->apiDataString($file), true);
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Facade;
use Tests\CreatesApplication;
use Tests\MockApiData;
use App\League;

class LeagueTest extends TestCase
{
    use CreatesApplication;
    use MockApiData;
    use RefreshDatabase;

    function testImport()
    {
        $json = $this->apiData('leagues.json');
        $data = $json[0];

        $obj = new League($data);
        $obj->save();

        $obj = League::find($data['id']);
        $this->assertTrue($obj !== null);
        $this->assertEquals($obj->name, $data['name']);
        $this->assertEquals($obj->url, $data['url']);
    }
}
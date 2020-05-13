<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Facade;
use Tests\CreatesApplication;
use Tests\MockApiData;
use App\League;

class MatchTest extends TestCase
{
    use CreatesApplication;
    use MockApiData;
    use RefreshDatabase;

    function testImport()
    {
        $json = $this->apiData('leagues.json');
        $data = $json[0];

        $league = new League($data);
        $league->save();

        $league = League::find($data['id']);
        $this->assertTrue($league !== null);
        $this->assertEquals($league->name, $data['name']);
        $this->assertEquals($league->slug, $data['slug']);
        $this->assertEquals($league->url, $data['url']);
    }
}

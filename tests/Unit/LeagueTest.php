<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Tests\ModelImportTest;
use App\League;

class LeagueTest extends TestCase
{
    use ModelImportTest;

    function testImport()
    {
        $json = $this->apiData('leagues.json');
        $data = $json[0];

        $obj = new League($data);
        $obj->save();
        $this->assertTrue($obj->id > 0);

        $obj = League::find($data['id']);
        $this->assertTrue($obj !== null);
        $this->assertEquals($obj->name, $data['name']);
        $this->assertEquals($obj->url, $data['url']);
    }
}

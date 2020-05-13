<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Tests\ModelImportTest;
use App\Team;

class TeamTest extends TestCase
{
    use ModelImportTest;

    function testImport()
    {
        $json = $this->apiData('teams.json');
        $data = $json[0];

        $obj = new Team($data);
        $obj->save();
        $this->assertTrue($obj->id > 0);

        $obj = Team::find($data['id']);
        $this->assertTrue($obj !== null);
        $this->assertEquals($obj->name, $data['name']);
        $this->assertEquals($obj->acronym, $data['acronym']);
    }
}

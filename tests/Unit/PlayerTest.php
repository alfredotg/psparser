<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Tests\ModelImportTest;
use App\Player;

class PlayerTest extends TestCase
{
    use ModelImportTest;

    function testImport()
    {
        $json = $this->apiData('players.json');
        $data = $json[0];

        $obj = new Player($data);
        $obj->save();

        $obj = Player::find($data['id']);
        $this->assertTrue($obj !== null);
        $this->assertEquals($obj->name, $data['name']);
        $this->assertEquals($obj->first_name, $data['first_name']);
        $this->assertEquals($obj->last_name, $data['last_name']);
    }
}

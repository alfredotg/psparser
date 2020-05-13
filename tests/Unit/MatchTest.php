<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Tests\ModelImportTest;
use App\Match;

class MatchTest extends TestCase
{
    use ModelImportTest;

    function testImport()
    {
        $json = $this->apiData('matches.json');
        $data = $json[0];

        $saved_tz = config('app.timezone');
        config(['app.timezone' => 'UTC']);

        $data['begin_at'] = '2020-05-05T18:42:00+0400';
        $match = new Match($data);
        $this->assertEquals($match->begin_at->format('Y-m-d H:i:s'), '2020-05-05 14:42:00');

        config(['app.timezone' => $saved_tz]);

        $match->save();

        $match = Match::find($data['id']);
        $this->assertTrue($match !== null);
        $this->assertEquals($match->begin_at->format('Y-m-d H:i:s'), '2020-05-05 14:42:00');
        $this->assertEquals($match->name, $data['name']);
        $this->assertEquals($match->forfeit, $data['forfeit']);
        $this->assertEquals($match->game_advantage, $data['game_advantage']);
        $this->assertEquals($match->league_id, $data['league_id']);
        $this->assertEquals($match->match_type, $data['match_type']);
    }
}

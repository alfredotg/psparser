<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Tests\CreatesApplication;
use Tests\MockApiData;
use App\Match;

class MatchTest extends TestCase
{
    use CreatesApplication;
    use MockApiData;
    use RefreshDatabase;

    function testImport()
    {
        $json = $this->apiData('matches.json');
        $match_data = $json[0];

        $saved_tz = config('app.timezone');
        config(['app.timezone' => 'UTC']);

        $match_data['begin_at'] = '2020-05-05T18:42:00+0400';
        $match = new Match($match_data);
        $this->assertEquals($match->begin_at->format('Y-m-d H:i:s'), '2020-05-05 14:42:00');

        config(['app.timezone' => $saved_tz]);

        $match->save();

        $match = Match::find($match_data['id']);
        $this->assertTrue($match !== null);
        $this->assertEquals($match->begin_at->format('Y-m-d H:i:s'), '2020-05-05 14:42:00');
        $this->assertEquals($match->name, $match_data['name']);
        $this->assertEquals($match->forfeit, $match_data['forfeit']);
        $this->assertEquals($match->game_advantage, $match_data['game_advantage']);
        $this->assertEquals($match->league_id, $match_data['league_id']);
        $this->assertEquals($match->match_type, $match_data['match_type']);
    }
}

<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;
use Tests\ModelImportTest;
use Tests\CreatesApplication;
use App\PandaScore\Api;
use App\Match;
use App\League;
use App\Player;
use App\Team;

class ParserTest extends TestCase
{
    use ModelImportTest;

    public function testSave()
    {
        Http::fakeSequence('*')
            ->push($this->apiDataString('matches.json'), 200);

        $this->artisan('pandascore:parse');

        $match = Match::find(560405);
        $this->assertIsObject($match);
        $league = $match->league;
        $this->assertIsObject($league);
        $this->assertEquals($league->id, 4243);

        $player = $match->players[0];
        $this->assertEquals('BLAST Rising', $player->name);

        $team = $match->teams[0];
        $this->assertEquals('Singularity', $team->name);
    }

    public function testFindingNew()
    {
        $match = new Match(['id' => 1, 'name' => "N0", 'match_type' => 'best_of', 'begin_at' => '2020-05-05T16:00:00']);
        $match->save();

        $req = null;
        Http::fake(function($request) use(&$req) {
            $req = $request;
            return Http::response('[]', 200);
        });

        $this->artisan('pandascore:parse');

        list($begin, $end) = explode(',', $req['range']['begin_at']);
        $this->assertEquals($begin, '2020-05-05T15:59:59Z');
    }
}

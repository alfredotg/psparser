<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Tests\CreatesApplication;
use App\PandaScore\Api;
use App\PandaScore\Sleeper;
use App\PandaScore\Request as ApiRequest;

class PandaScoreApiTest extends TestCase
{
    use CreatesApplication;

    public function testMatchesGet()
    {
        $this->_fakeFromFile('matches*', 'matches.json');

        $api = App::make(Api::class);
        $mathes = $api->matches()->get();
        $this->assertEquals(1, count($mathes));
        $this->assertEquals(560405, $mathes[0]['id']);
    }

    public function testMatchesCursor()
    {
        $fake = $this->_fakeFromFile('*', 'matches.json');
        $api = App::make(Api::class);
        $mathes = [];
        foreach($api->matches()->perPage(1)->cursor() as $math)
        {
            $mathes[] = $math;
            if(count($mathes) == 1)
                $fake->push('[{}]');
            else
                $fake->push('[]'); // end
        }
        $this->assertEquals(count($mathes), 2);
    }

    public function testException() 
    {
        Http::fakeSequence()->push('Error', 404); 
        $api = App::make(Api::class);
        try {
            $mathes = $api->matches()->get();
            $this->assertTrue(false);
        } catch(\Illuminate\Http\Client\RequestException $e) {
            $this->assertTrue(true);
        }
    }

    public function testRateLimit()
    {
        Http::fakeSequence()
            ->push('Error', 445, ['X-Rate-Limit-Remaining' => '0']) 
            ->push('Error', 445, ['X-Rate-Limit-Remaining' => '0']) 
            ->push('[]', 200); 
        $api = App::make(Api::class);
        $sleeper = new class {                        
            public $seconds = 0;

            public function sleep(int $seconds): void
            {
                $this->seconds += $seconds;
            }
        };
        $this->app->singleton(Sleeper::class, function() use($sleeper) { return $sleeper; });
        $api->some()->get();
        $this->assertEquals($sleeper->seconds, ApiRequest::EXCESS_OF_LIMIT_SLEEP_SECONDS * 2 + 1); // sleep twice plus two tries 

    } 

    private function _fakeFromFile(string $path, string $file)
    {
        return Http::fakeSequence($path)
            ->push(file_get_contents(__DIR__ . '/json/' . $file), 200);
    }
}

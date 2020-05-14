<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Tests\CreatesApplication;
use Tests\MockApiData;
use App\PandaScore\Api;
use App\PandaScore\Sleeper;
use App\PandaScore\Request as ApiRequest;

class PandaScoreApiTest extends TestCase
{
    use CreatesApplication;
    use MockApiData;

    public function testMatchesGet()
    {
        $this->_fakeFromFile('matches*', 'matches.json');
        $json = $this->apiData('matches.json');

        $api = App::make(Api::class);
        $matches = $api->matches()->get();
        $this->assertEquals(1, count($matches));
        $this->assertEquals($json[0]['id'], $matches[0]['id']);
    }

    public function testMatchesCursor()
    {
        $fake = $this->_fakeFromFile('*', 'matches.json');
        $api = App::make(Api::class);
        $matches = [];
        foreach($api->matches()->perPage(1)->cursor() as $math)
        {
            $matches[] = $math;
            if(count($matches) == 1)
                $fake->push('[{}]');
            else
                $fake->push('[]'); // end
        }
        $this->assertEquals(count($matches), 2);
    }

    public function testException() 
    {
        Http::fakeSequence()->push('Error', 404); 
        $api = App::make(Api::class);
        try {
            $matches = $api->matches()->get();
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
        $this->app->singleton(Sleeper::class, function() use($sleeper) {
            return $sleeper; 
        });
        $api->some()->get();
        $this->assertEquals($sleeper->seconds, ApiRequest::EXCESS_OF_LIMIT_SLEEP_SECONDS * 3); 

    } 

    private function _fakeFromFile(string $path, string $file)
    {
        return Http::fakeSequence($path)
            ->push($this->apiDataString($file), 200);
    }
}

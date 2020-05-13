<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Tests\CreatesApplication;
use App\PandaScore\Api;

class PandaScoreApiTest extends TestCase
{
    use CreatesApplication;

    public function testMathesGet()
    {
        $this->_fakeFromFile('mathes*', 'mathes.json');

        $api = App::make(Api::class);
        $mathes = $api->mathes()->get();
        $this->assertEquals(1, count($mathes));
        $this->assertEquals(560405, $mathes[0]['id']);
    }

    public function testMathesCursor()
    {
        $fake = $this->_fakeFromFile('*', 'mathes.json');
        $api = App::make(Api::class);
        $mathes = [];
        foreach($api->mathes()->perPage(1)->cursor() as $math)
        {
            $mathes[] = $math;
            if(count($mathes) == 1)
                $fake->push('[{}]');
            else
                $fake->push('[]'); // end
        }
        $this->assertEquals(count($mathes), 2);
    }

    private function _fakeFromFile(string $path, string $file)
    {
        return Http::fakeSequence($path)
            ->push(file_get_contents(__DIR__ . '/json/' . $file), 200);
    }
}

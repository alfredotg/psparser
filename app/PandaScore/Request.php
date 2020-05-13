<?php

namespace App\PandaScore;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\App;

class Request
{
    const EXCESS_OF_LIMIT_SLEEP_SECONDS = 5;
    const MAX_TRIES = 3;
    
    private $paht;
    private $query;
    private $page = null;
    private $per_page = null;

    function __construct(string $path, array $query)
    {
        $this->path = $path;
        $this->query = $query;
    }

    function page(int $page): Request
    {
        assert($page >= 0);
        $this->page = $page;
        return $this;
    }

    function perPage(int $per_page): Request
    {
        assert($per_page > 0 && $per_page <= 100);
        $this->per_page = $per_page;
        return $this;
    }

    function cursor(): iterable 
    {
        if($this->per_page === null)
            $this->per_page = 100;
        if($this->page === null)
            $this->page = 1;
        while(true) 
        {
            $data = $this->get();
            if(!is_array($data))
                throw new \Exception("Response is not array");
            foreach($data as $row)
                yield $row;
            if(count($data) < $this->per_page)
                break;
            $this->page++;
        }
    }

    function query($param, $value)
    {
        $this->query[$param] = $value;
        return $this;
    }

    function get()
    {
        $query = $this->query;
        if($this->per_page !== null)
            $query['per_page'] = $this->per_page;
        if($this->page !== null)
            $query['page'] = $this->page;

        $url = $this->path . '?' . http_build_query($query); 
        $res = null;
        foreach(range(0, self::MAX_TRIES - 1) as $try) 
        {
            $res = Http::timeout(10)->get($url);
            $rlimit = $res->header('X-Rate-Limit-Remaining');
            if($res->status() == 200)
                break;
            $rlimit = $res->header('X-Rate-Limit-Remaining');
            if($rlimit === '0')
            {
                App::make(Sleeper::class)->sleep(self::EXCESS_OF_LIMIT_SLEEP_SECONDS + $try);
                continue;
            }
            $res->throw();
            break;
        }
        return $res->json();
    }
}

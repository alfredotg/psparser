<?php

namespace App\PandaScore;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class Request
{
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
        while(true) {
            $data = $this->get();
            if(!is_array($data))
            {
                yield $data;
                break;
            }
            foreach($data as $row)
                yield $row;
            if(count($data) < $this->per_page)
                break;
            $this->page++;
        }
    }

    function get()
    {
        $query = $this->query;
        if($this->per_page !== null)
            $query['per_page'] = $this->per_page;
        if($this->page !== null)
            $query['page'] = $this->page;

        $url = $this->path . '?' . http_build_query($query); 
        $res = Http::get($url);
        $res->throw();
        return $res->json();
    }
}

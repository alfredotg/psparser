<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\PandaScore\Api;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use App\Match;
use App\Player;
use App\Team;
use App\League;

class Parse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pandascore:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parse and save PandaScore new matches';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $api = App::make(Api::class);
        $matches  = $api->matches()->query('sort', 'begin_at');

        $last_match = Match::orderBy('begin_at', 'DESC')->first();
        if($last_match !== null)
        {
            $begin = $last_match->begin_at->sub(new \DateInterval('PT1S'));
            $end = now();
            $matches->query('range[begin_at]', $begin->format('Y-m-d\TH:i:s\Z').','.$end->format('Y-m-d\TH:i:s\Z'));
        }

        foreach($matches->cursor() as $data)
        {
            DB::transaction(function () use($data) {
                $this->_saveMatch($data);
            });
        }
    }

    protected function _saveMatch(array $data): void
    {
        if(Match::find($data['id']) != null)
        {
            $this->info(sprintf("Skipping existing #%s", $data['id'])); 
            return;
        }
        $match = new Match($data);
        $match->save();
        $this->info(sprintf("Save new match #%s", $match->id)); 

        $opponents = [];
        $players = [];
        $teams = [];
        foreach($data['opponents'] as $opponent_data)
        {
            $opponent = $this->_saveOpponent($opponent_data, $match);
            if($opponent !== null)
                $opponents[] = $opponent;

            if($opponent instanceof Player)
                $players[] = $opponent->id;
            else if($opponent instanceof Team)
                $teams[] = $opponent->id;

        }
        assert(count($opponents) > 1);

        $match->teams()->sync(array_unique($teams));
        $match->players()->sync(array_unique($players));

        $league = $this->_saveLeague($data['league']);
        assert($match->league_id == $league->id);
    }

    protected function _saveLeague(array $data)
    {
        return League::firstOrCreate(['id' => $data['id']], $data);
    }

    protected function _saveOpponent(array $data, Match $match)
    {
        $type = $data['type'];
        $data = $data['opponent'];
        switch($type)
        {                                                      
            case 'Player':
                $player = Player::firstOrCreate(['id' => $data['id']], $data);
                return $player;
            case 'Team':
                $team = Team::firstOrCreate(['id' => $data['id']], $data);
                return $team;
        }
    }
}

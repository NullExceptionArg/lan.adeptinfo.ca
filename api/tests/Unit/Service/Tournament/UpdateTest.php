<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $user;
    protected $lan;
    protected $tournament;

    protected $paramsContent = [
        'tournament_id' => null,
        'name' => 'October',
        'state' => 'visible',
        'tournament_start' => null,
        'tournament_end' => null,
        'players_to_reach' => 5,
        'teams_to_reach' => 6,
        'rules' => 'The Bolsheviks seize control of Petrograd.',
        'price' => 0,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->paramsContent['lan_id'] = $this->lan->id;
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->paramsContent['tournament_start'] = $startTime->addHour(1)->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->paramsContent['tournament_end'] = $endTime->subHour(1)->format('Y-m-d H:i:s');
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
    }

    public function testUpdate(): void
    {
        $result = $this->tournamentService->update(
            $this->tournament->id,
            $this->paramsContent['name'],
            Carbon::parse($this->paramsContent['tournament_start']),
            Carbon::parse($this->paramsContent['tournament_end']),
            $this->paramsContent['players_to_reach'],
            $this->paramsContent['teams_to_reach'],
            $this->paramsContent['state'],
            $this->paramsContent['rules'],
            $this->paramsContent['price']
        );

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->paramsContent['state'], $result->state);
        $this->assertEquals($this->paramsContent['lan_id'], $result->lan_id);
        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->paramsContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->paramsContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->paramsContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->paramsContent['rules'], $result->rules);
        $this->assertEquals($this->paramsContent['price'], $result->price);
    }
}

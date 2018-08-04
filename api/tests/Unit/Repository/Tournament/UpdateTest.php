<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;
use DateTime;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'lan' => null,
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
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $startTime = new Carbon($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(1);
        $endTime = new Carbon($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour(1);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $this->requestContent['tournament'] = $this->tournament;

        $this->be($this->user);
    }

    public function testUpdate(): void
    {
        $result = $this->tournamentRepository->update(
            $this->requestContent['tournament'],
            $this->requestContent['name'],
            $this->requestContent['state'],
            new DateTime($this->requestContent['tournament_start']),
            new DateTime($this->requestContent['tournament_end']),
            $this->requestContent['players_to_reach'],
            $this->requestContent['teams_to_reach'],
            $this->requestContent['rules'],
            $this->requestContent['price']
        );

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->lan->id, $result->lan_id);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->requestContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->requestContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->requestContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->requestContent['rules'], $result->rules);
        $this->assertEquals($this->requestContent['price'], $result->price);
    }
}

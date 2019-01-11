<?php

namespace Tests\Unit\Repository;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'lan' => null,
        'name' => 'October',
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

        $this->requestContent['lan'] = $this->lan;
        $startTime = Carbon::parse($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(1);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour(1);

        $this->be($this->user);
    }

    public function testCreate(): void
    {
        $this->notSeeInDatabase('tournament', [
            'name' => $this->requestContent['name']
        ]);

        $result = $this->tournamentRepository->create(
            $this->requestContent['lan'],
            $this->requestContent['name'],
            $this->requestContent['tournament_start'],
            $this->requestContent['tournament_end'],
            $this->requestContent['players_to_reach'],
            $this->requestContent['teams_to_reach'],
            $this->requestContent['rules'],
            $this->requestContent['price']
        );

        $this->seeInDatabase('tournament', [
            'name' => $this->requestContent['name']
        ]);

        $this->assertIsInt($result);
    }
}

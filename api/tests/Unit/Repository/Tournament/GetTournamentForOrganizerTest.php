<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTournamentForOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->user->id,
            'tournament_id' => $this->tournament->id
        ]);
        $this->be($this->user);
    }

    public function testGetTournamentForOrganizer(): void
    {
        $result = $this->tournamentRepository->getTournamentsForOrganizer($this->user, $this->lan);

        $this->assertEquals($this->tournament->id, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['name']);
    }
}

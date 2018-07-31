<?php

namespace Tests\Unit\Repository\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AssociateOrganizerTournamentTest extends TestCase
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

        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $this->be($this->user);
    }

    public function testAssociateOrganizerTournament(): void
    {
        $this->notSeeInDatabase('organizer_tournament', [
            'id' => 1,
            'organizer_id' => $this->user->id,
            'tournament_id' => $this->tournament->id
        ]);

        $this->tournamentRepository->associateOrganizerTournament($this->user, $this->tournament);

        $this->seeInDatabase('organizer_tournament', [
            'id' => 1,
            'organizer_id' => $this->user->id,
            'tournament_id' => $this->tournament->id
        ]);
    }
}

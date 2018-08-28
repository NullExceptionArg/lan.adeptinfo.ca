<?php

namespace Tests\Unit\Repository\Tournament;

use App\Model\OrganizerTournament;
use App\Model\Request;
use App\Model\TagTeam;
use App\Model\Team;
use App\Model\Tournament;
use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentRepository;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $tagTeam;
    protected $request;
    protected $organizer;
    protected $organizerTournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentRepository = $this->app->make('App\Repositories\Implementation\TournamentRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id
        ]);

        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);

        $this->tagTeam = factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);

        $this->request = factory('App\Model\Request')->create([
            'tag_id' => $tag2->id,
            'team_id' => $this->team->id
        ]);

        $this->organizer = factory('App\Model\User')->create();
        $this->organizerTournament = factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->organizer->id,
            'tournament_id' => $this->tournament->id
        ]);
    }

    public function testDelete(): void
    {
        $this->tournamentRepository->delete($this->tournament);

        $tournament = Tournament::withTrashed()->first();
        $team = Team::withTrashed()->first();
        $tagTeam = TagTeam::withTrashed()->first();
        $request = Request::withTrashed()->first();
        $organizerTournament = OrganizerTournament::withTrashed()->first();

        $this->assertEquals($this->tournament->id, $tournament->id);
        $this->assertEquals($this->team->id, $team->id);
        $this->assertEquals($this->tagTeam->id, $tagTeam->id);
        $this->assertEquals($this->request->id, $request->id);
        $this->assertEquals($this->organizerTournament->id, $organizerTournament->id);
    }
}

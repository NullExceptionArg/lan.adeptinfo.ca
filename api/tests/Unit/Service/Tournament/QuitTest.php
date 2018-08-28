<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class QuitTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $organizer;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->organizer = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'teams_to_reach' => 10,
            'players_to_reach' => 10
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->organizer->id,
            'tournament_id' => $this->tournament->id
        ]);
    }

    public function testQuit(): void
    {
        $this->be($this->organizer);
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $organizer2->id,
            'tournament_id' => $this->tournament->id
        ]);

        $result = $this->tournamentService->quit($this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals('hidden', $result->state);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }

    public function testQuitLastOrganizer(): void
    {
        $this->be($this->organizer);
        $result = $this->tournamentService->quit($this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals('hidden', $result->state);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }

    public function testQuitTournamentIdExist(): void
    {
        $this->be($this->organizer);
        try {
            $this->tournamentService->quit(-1);
            $this->fail('Expected: {"tournament_id":["The selected tournament id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The selected tournament id is invalid."]}', $e->getMessage());
        }
    }

    public function testQuitOrganizerHasTournament(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        try {
            $this->tournamentService->quit($this->tournament->id);
            $this->fail('Expected: {"tournament_id":["The user doesn\'t have any tournaments"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The user doesn\'t have any tournaments"]}', $e->getMessage());
        }
    }
}

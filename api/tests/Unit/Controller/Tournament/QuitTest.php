<?php

namespace Tests\Unit\Controller\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class QuitTest extends TestCase
{
    use DatabaseMigrations;

    protected $organizer;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();

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
        $organizer2 = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $organizer2->id,
            'tournament_id' => $this->tournament->id
        ]);

        $this->actingAs($this->organizer)
            ->json('POST', '/api/tournament/quit/' . $this->tournament->id)
            ->seeJsonEquals([
                'id' => $this->tournament->id,
                'lan_id' => $this->tournament->lan_id,
                'name' => $this->tournament->name,
                'price' => $this->tournament->price,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'players_to_reach' => $this->tournament->players_to_reach,
                'teams_to_reach' => $this->tournament->teams_to_reach,
                'state' => 'hidden',
                'rules' => $this->tournament->rules
            ])
            ->assertResponseStatus(200);
    }

    public function testQuitLastOrganizer(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', '/api/tournament/quit/' . $this->tournament->id)
            ->seeJsonEquals([
                'id' => $this->tournament->id,
                'lan_id' => $this->tournament->lan_id,
                'name' => $this->tournament->name,
                'price' => $this->tournament->price,
                'tournament_start' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)),
                'tournament_end' => date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)),
                'players_to_reach' => $this->tournament->players_to_reach,
                'teams_to_reach' => $this->tournament->teams_to_reach,
                'state' => 'hidden',
                'rules' => $this->tournament->rules
            ])
            ->assertResponseStatus(200);
    }

    public function testQuitTournamentIdExist(): void
    {
        $this->actingAs($this->organizer)
            ->json('POST', '/api/tournament/quit/' . -1)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The selected tournament id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testQuitOrganizerHasTournament(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/tournament/quit/' . -1)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tournament_id' => [
                        0 => 'The selected tournament id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

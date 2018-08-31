<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteAdminTest extends TestCase
{
    use DatabaseMigrations;

    protected $organizer;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'team_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->lan = factory('App\Model\Lan')->create();
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);
        $this->organizer = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->organizer->id,
            'tournament_id' => $this->tournament->id
        ]);

        $this->requestContent['team_id'] = $this->team->id;
    }

    public function testDeleteAdmin(): void
    {
        $this->actingAs($this->organizer)
            ->json('DELETE', '/api/team/admin', $this->requestContent)
            ->seeJsonEquals([
                'id' => $this->team->id,
                'name' => $this->team->name,
                'tag' => $this->team->tag,
                'tournament_id' => $this->team->tournament_id
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteAdminTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->organizer)
            ->json('DELETE', '/api/team/admin', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteAdminTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->organizer)
            ->json('DELETE', '/api/team/admin', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The selected team id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteAdminTeamIdUserIsTournamentAdmin(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', '/api/team/admin', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The user doesn\'t have any tournaments'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'team_id' => null,
        'tag_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
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
        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->requestContent['tag_id'] = $this->tag->id;
    }

    public function testCreate(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'team_id' => $this->requestContent['team_id'],
                'tag_id' => $this->requestContent['tag_id'],
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateTeamIdRequired(): void
    {
        $this->requestContent['team_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id field is required.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
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

    public function testCreateUserTagIdRequired(): void
    {
        $this->requestContent['tag_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag id field is required.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateUserTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The selected tag id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestUserTagIdUniqueUserPerTournamentSameUser(): void
    {
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id' => $tag->id,
                'name' => 'name',
                'tag' => 'tag'
            ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'A user can only be once in a tournament.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRequestUserTagIdUniqueUserPerTournamentSameLan(): void
    {
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team', [
                'tournament_id' => $tournament->id,
                'user_tag_id' => $this->tag->id,
                'name' => 'name',
                'tag' => 'tag'
            ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'team_id' => $this->requestContent['team_id'],
                'tag_id' => $this->requestContent['tag_id']
            ])
            ->assertResponseStatus(201);
    }
}

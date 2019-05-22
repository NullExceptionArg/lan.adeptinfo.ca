<?php

namespace Tests\Unit\Controller\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class KickTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $leader;
    protected $userTag;
    protected $leaderTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $userTagTeam;

    protected $requestContent = [
        'team_id' => null,
        'tag_id'  => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->userTag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id,
        ]);
        $this->leader = factory('App\Model\User')->create();
        $this->leaderTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id,
        ]);

        $this->lan = factory('App\Model\Lan')->create();

        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id'           => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end'   => $endTime->subHour(1),
        ]);

        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id,
        ]);

        $this->userTagTeam = factory('App\Model\TagTeam')->create([
            'tag_id'  => $this->userTag->id,
            'team_id' => $this->team->id,
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id'    => $this->leaderTag->id,
            'team_id'   => $this->team->id,
            'is_leader' => true,
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->requestContent['tag_id'] = $this->userTag->id;
    }

    public function testKick(): void
    {
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'id'   => $this->userTag->id,
                'name' => $this->userTag->name,
            ])
            ->assertResponseStatus(200);
    }

    public function testKickTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The team id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testKickTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'team_id' => [
                        0 => 'The selected team id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testKickTeamIdUserIsTeamLeader(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testKickTagIdInteger(): void
    {
        $this->requestContent['tag_id'] = '☭';
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testKickTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The selected tag id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testKickTagIdTagBelongsInTeam(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id,
        ]);
        $this->requestContent['tag_id'] = $tag->id;

        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag must be in the team.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testKickTagIdTagNotBelongsLeader(): void
    {
        $this->requestContent['tag_id'] = $this->leaderTag->id;
        $this->actingAs($this->leader)
            ->json('DELETE', 'http://'.env('API_DOMAIN').'/team/kick', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'tag_id' => [
                        0 => 'The tag must not belong to the leader of the team.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}

<?php

namespace Tests\Unit\Controller\Tournament;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;

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
    }

    public function testDelete(): void
    {
        $user2 = factory('App\Model\User')->create();
        $tag2 = factory('App\Model\Tag')->create([
            'user_id' => $user2->id
        ]);

        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);

        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $team->id,
            'is_leader' => true
        ]);

        factory('App\Model\Request')->create([
            'tag_id' => $tag2->id,
            'team_id' => $team->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', '/api/tournament/' . $this->tournament->id)
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

    public function testDeleteTournamentIdExit(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/tournament/' . -1)
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

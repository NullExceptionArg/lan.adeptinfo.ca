<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUserSummaryTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament1;
    protected $tournament2;
    protected $tournament3;
    protected $team1;
    protected $team2;
    protected $team3;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;

        $this->be($this->user);
    }

    public function testGetUserSummary(): void
    {
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament1 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament2 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament3 = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $this->team1 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament1->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team1->id,
            'is_leader' => true
        ]);
        $this->team2 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament2->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team2->id,
            'is_leader' => true
        ]);
        $this->team3 = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament3->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team3->id,
            'is_leader' => false
        ]);

        for ($i = 0; $i < 3; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team1->id
            ]);
        }

        for ($i = 0; $i < 2; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team2->id
            ]);
        }

        for ($i = 0; $i < 4; $i++) {
            $user = factory('App\Model\User')->create();
            $tag = factory('App\Model\Tag')->create([
                'user_id' => $user->id
            ]);
            factory('App\Model\Request')->create([
                'tag_id' => $tag->id,
                'team_id' => $this->team3->id
            ]);
        }

        $result = $this->userService->getUserSummary($this->lan->id)->jsonSerialize();

        $this->assertEquals($this->user->first_name, $result['first_name']);
        $this->assertEquals($this->user->last_name, $result['last_name']);
        $this->assertEquals(5, $result['request_count']);
    }
}

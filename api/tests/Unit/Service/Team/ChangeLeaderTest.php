<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class ChangeLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $leader;
    protected $toBeLeader;
    protected $leadersTag;
    protected $toBeLeadersTag;
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
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id
        ]);
        $this->toBeLeader = factory('App\Model\User')->create();
        $this->toBeLeadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->toBeLeader->id
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

        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->leadersTag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->toBeLeadersTag->id,
            'team_id' => $this->team->id,
            'is_leader' => false
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->requestContent['tag_id'] = $this->toBeLeadersTag->id;

        $this->be($this->leader);
    }

    public function testChangeLeader(): void
    {
        $result = $this->teamService->changeLeader($request);

        $this->assertEquals($this->toBeLeadersTag->id, $result->id);
        $this->assertEquals($this->toBeLeadersTag->name, $result->name);
    }
}

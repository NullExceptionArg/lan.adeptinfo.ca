<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $leader;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

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
        $this->leader = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);
    }

    public function testDeleteLeader(): void
    {
        $result = $this->teamService->deleteLeader($this->team->id);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }
}

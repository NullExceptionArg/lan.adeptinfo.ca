<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class LeaveTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $leader;
    protected $userTag;
    protected $leaderTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $userTagTeam;

    protected $requestContent = [
        'team_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->userTag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $this->leader = factory('App\Model\User')->create();
        $this->leaderTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id
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

        $this->userTagTeam = factory('App\Model\TagTeam')->create([
            'tag_id' => $this->userTag->id,
            'team_id' => $this->team->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $this->leaderTag->id,
            'team_id' => $this->team->id,
            'is_leader' => true
        ]);

        $this->requestContent['team_id'] = $this->team->id;
    }

    public function testLeave(): void
    {
        $this->be($this->user);
        $request = new Request($this->requestContent);
        $result = $this->teamService->leave($request);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testLeaveIsLeader(): void
    {
        $this->be($this->leader);
        $request = new Request($this->requestContent);
        $result = $this->teamService->leave($request);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testLeaveLeaderLastPlayer(): void
    {
        $this->userTagTeam->delete();
        $this->userTag->delete();
        $this->user->delete();
        $this->be($this->leader);
        $request = new Request($this->requestContent);
        $result = $this->teamService->leave($request);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testLeaveTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $this->be($this->user);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->leave($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testLeaveTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $this->be($this->user);
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->leave($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }
}

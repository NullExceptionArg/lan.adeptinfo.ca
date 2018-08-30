<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteRequestLeaderTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $leader;
    protected $requestingUser;
    protected $leadersTag;
    protected $requestingUsersTag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $request;

    protected $requestContent = [
        'request_id' => null,
        'team_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

        $this->leader = factory('App\Model\User')->create();
        $this->leadersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->leader->id
        ]);
        $this->requestingUser = factory('App\Model\User')->create();
        $this->requestingUsersTag = factory('App\Model\Tag')->create([
            'user_id' => $this->requestingUser->id
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
        $this->request = factory('App\Model\Request')->create([
            'tag_id' => $this->requestingUsersTag->id,
            'team_id' => $this->team->id
        ]);

        $this->requestContent['request_id'] = $this->request->id;
        $this->requestContent['team_id'] = $this->team->id;

        $this->be($this->leader);
    }

    public function testDeleteRequestLeader(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->deleteRequestLeader($request);

        $this->assertEquals($this->requestingUsersTag->id, $result->id);
        $this->assertEquals($this->requestingUsersTag->name, $result->name);
    }

    public function testDeleteRequestLeaderRequestIdInteger(): void
    {
        $this->requestContent['request_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: {"request_id":["The request id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"request_id":["The request id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteRequestLeaderRequestIdExist(): void
    {
        $this->requestContent['request_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: {"request_id":["The selected request id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"request_id":["The selected request id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteRequestLeaderRequestIdRequestBelongsInTeam(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);
        $request = factory('App\Model\Request')->create([
            'tag_id' => $tag,
            'team_id' => $team->id
        ]);
        $this->requestContent['request_id'] = $request->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: {"request_id":["The request must be for the leaders team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"request_id":["The request must be for the leaders team."]}', $e->getMessage());
        }
    }

    public function testDeleteRequestLeaderTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteRequestLeaderTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteRequestLeaderTeamIdUserIsTeamLeader(): void
    {
        $this->be($this->requestingUser);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteRequestLeader($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }
}

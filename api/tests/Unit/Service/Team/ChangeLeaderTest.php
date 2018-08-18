<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        $request = new Request($this->requestContent);
        $result = $this->teamService->changeLeader($request);

        $this->assertEquals($this->toBeLeadersTag->id, $result->id);
        $this->assertEquals($this->toBeLeadersTag->name, $result->name);
    }

    public function testChangeLeaderTagIdInteger(): void
    {
        $this->requestContent['tag_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"tag_id":["The tag id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag id must be an integer."]}', $e->getMessage());
        }
    }

    public function testChangeLeaderTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"tag_id":["The selected tag id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The selected tag id is invalid."]}', $e->getMessage());
        }
    }

    public function testChangeLeaderTagIdTagBelongsInTeam(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $this->requestContent['tag_id'] = $tag->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"tag_id":["The tag must be in the team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag must be in the team."]}', $e->getMessage());
        }
    }

    public function testChangeLeaderTagIdTagNotBelongsLeader(): void
    {
        $this->requestContent['tag_id'] = $this->leadersTag->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"tag_id":["The tag must not belong to the leader of the team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag must not belong to the leader of the team."]}', $e->getMessage());
        }
    }


    public function testChangeLeaderTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testChangeLeaderTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testChangeLeaderTeamIdUserIsTeamLeader(): void
    {
        $this->be($this->toBeLeader);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->changeLeader($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }
}

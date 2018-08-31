<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class KickTest extends TestCase
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
        'team_id' => null,
        'tag_id' => null
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
        $this->requestContent['tag_id'] = $this->userTag->id;
        $this->be($this->leader);
    }

    public function testKick(): void
    {
        $request = new Request($this->requestContent);
        $tag = $this->teamService->kick($request);

        $this->assertEquals($this->userTag->id, $tag->id);
        $this->assertEquals($this->userTag->name, $tag->name);
    }

    public function testKickTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testKickTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testKickTeamIdUserIsTeamLeader(): void
    {
        $this->be($this->user);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testKickTagIdInteger(): void
    {
        $this->requestContent['tag_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"tag_id":["The tag id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag id must be an integer."]}', $e->getMessage());
        }
    }

    public function testKickTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"tag_id":["The selected tag id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The selected tag id is invalid."]}', $e->getMessage());
        }
    }

    public function testKickTagIdTagBelongsInTeam(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $this->requestContent['tag_id'] = $tag->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"tag_id":["The tag must be in the team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag must be in the team."]}', $e->getMessage());
        }
    }

    public function testKickTagIdTagNotBelongsLeader(): void
    {
        $this->requestContent['tag_id'] = $this->leaderTag->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->kick($request);
            $this->fail('Expected: {"tag_id":["The tag must not belong to the leader of the team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag must not belong to the leader of the team."]}', $e->getMessage());
        }
    }
}
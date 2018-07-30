<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

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
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

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
        $request = new Request($this->requestContent);
        $result = $this->teamService->createRequest($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['team_id'], $result->team_id);
        $this->assertEquals($this->requestContent['tag_id'], $result->tag_id);
    }

    public function testCreateTeamIdRequired(): void
    {
        $this->requestContent['team_id'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"team_id":["The team id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdRequired(): void
    {
        $this->requestContent['tag_id'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"tag_id":["The tag id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The tag id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdExist(): void
    {
        $this->requestContent['tag_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"tag_id":["The selected tag id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["The selected tag id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateUserUniqueUserPerRequest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/team/request', $this->requestContent);
        $this->requestContent['tag_id'] = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ])->id;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"team_id":["A user can only have one request per team."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["A user can only have one request per team."]}', $e->getMessage());
        }
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
        $request = new Request($this->requestContent);
        try {
            $this->teamService->createRequest($request);
            $this->fail('Expected: {"tag_id":["A user can only be once in a tournament."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag_id":["A user can only be once in a tournament."]}', $e->getMessage());
        }
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
        $request = new Request($this->requestContent);
        $result = $this->teamService->createRequest($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['team_id'], $result->team_id);
        $this->assertEquals($this->requestContent['tag_id'], $result->tag_id);
    }
}

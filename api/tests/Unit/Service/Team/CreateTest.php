<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'tournament_id' => null,
        'user_tag_id' => null,
        'name' => 'WorkersUnite',
        'tag' => 'PRO',
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

        $this->requestContent['tournament_id'] = $this->tournament->id;
        $this->requestContent['user_tag_id'] = $this->tag->id;
    }

    public function testCreate(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->create($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['tag'], $result->tag);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_id'], $result->tournament_id);
    }

    public function testCreateTournamentIdRequired(): void
    {
        $this->requestContent['tournament_id'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"tournament_id":["The tournament id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The tournament id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateTournamentIdExist(): void
    {
        $this->requestContent['tournament_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"tournament_id":["The selected tournament id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The selected tournament id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdRequired(): void
    {
        $this->requestContent['user_tag_id'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"user_tag_id":["The user tag id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_tag_id":["The user tag id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdExist(): void
    {
        $this->requestContent['user_tag_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"user_tag_id":["The selected user tag id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_tag_id":["The selected user tag id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdUniqueUserPerTournamentSameTag(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id' => $this->tag->id,
                'name' => 'name',
                'tag' => 'tag'
            ]);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"user_tag_id":["A user can only be once in a tournament."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_tag_id":["A user can only be once in a tournament."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdUniqueUserPerTournamentSameUser(): void
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
            $this->teamService->create($request);
            $this->fail('Expected: {"user_tag_id":["A user can only be once in a tournament."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_tag_id":["A user can only be once in a tournament."]}', $e->getMessage());
        }
    }

    public function testCreateUserTagIdUniqueUserPerTournamentSameLan(): void
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
        $result = $this->teamService->create($request);

        $this->assertEquals(2, $result->id);
        $this->assertEquals($this->requestContent['tag'], $result->tag);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_id'], $result->tournament_id);
    }

    public function testCreateNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"name":["The name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateNameUniqueTeamNamePerTournament(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id' => $tag->id,
                'name' => $this->requestContent['name'],
                'tag' => 'tag'
            ]);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"name":["A team name must be unique per lan."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["A team name must be unique per lan."]}', $e->getMessage());
        }
    }

    public function testCreateNameUniqueTeamNamePerTournamentSameLan(): void
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
                'name' => $this->requestContent['name'],
                'tag' => 'tag'
            ]);
        $request = new Request($this->requestContent);
        $result = $this->teamService->create($request);

        $this->assertEquals(2, $result->id);
        $this->assertEquals($this->requestContent['tag'], $result->tag);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_id'], $result->tournament_id);
    }

    public function testCreateTagString(): void
    {
        $this->requestContent['tag'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"tag":["The tag must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag":["The tag must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateTagMaxLength(): void
    {
        $this->requestContent['tag'] = str_repeat('☭', 6);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"tag":["The tag may not be greater than 5 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag":["The tag may not be greater than 5 characters."]}', $e->getMessage());
        }
    }

    public function testCreateTagUniqueTeamTagPerTournament(): void
    {
        $user = factory('App\Model\User')->create();
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/team', [
                'tournament_id' => $this->tournament->id,
                'user_tag_id' => $tag->id,
                'name' => 'name',
                'tag' => $this->requestContent['tag']
            ]);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->create($request);
            $this->fail('Expected: {"tag":["A team tag must be unique per lan."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tag":["A team tag must be unique per lan."]}', $e->getMessage());
        }
    }

    public function testCreateTagUniqueTeamTagPerTournamentSameLan(): void
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
                'tag' => $this->requestContent['tag']
            ]);
        $request = new Request($this->requestContent);
        $result = $this->teamService->create($request);

        $this->assertEquals(2, $result->id);
        $this->assertEquals($this->requestContent['tag'], $result->tag);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_id'], $result->tournament_id);
    }
}

<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetUserTeamsTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;

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

        $this->be($this->user);
    }

    public function testCreateRequestNotConfirmed(): void
    {
        factory('App\Model\Request')->create([
            'team_id' => $this->team->id,
            'tag_id' => $this->tag->id
        ]);

        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('not-confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testCreateRequestCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $startTime = new Carbon($lan->lan_start);
        $endTime = new Carbon($lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id
        ]);
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        factory('App\Model\Request')->create([
            'team_id' => $team->id,
            'tag_id' => $tag->id
        ]);

        $request = new Request([
            'lan_id' => null
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals(2, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('not-confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testCreateLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->teamService->getUserTeams($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->teamService->getUserTeams($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateRequestConfirmed(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id' => $this->tag->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testCreateRequestLeader(): void
    {
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id' => $this->tag->id,
            'is_leader' => true
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('leader', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);
    }

    public function testCreateRequestManyTeams(): void
    {
        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $tournament->id
        ]);
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $team->id,
            'tag_id' => $tag->id
        ]);
        factory('App\Model\TagTeam')->create([
            'team_id' => $this->team->id,
            'tag_id' => $this->tag->id
        ]);

        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals(1, $result[0]->jsonSerialize()['id']);
        $this->assertEquals($this->team->name, $result[0]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[0]->jsonSerialize()['player_state']);
        $this->assertEquals($this->tournament->players_to_reach, $result[0]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[0]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[0]->jsonSerialize()['requests']);
        $this->assertEquals($this->team->tag, $result[0]->jsonSerialize()['tag']);
        $this->assertEquals($this->tournament->name, $result[0]->jsonSerialize()['tournament_name']);

        $this->assertEquals(2, $result[1]->jsonSerialize()['id']);
        $this->assertEquals($team->name, $result[1]->jsonSerialize()['name']);
        $this->assertEquals('confirmed', $result[1]->jsonSerialize()['player_state']);
        $this->assertEquals($tournament->players_to_reach, $result[1]->jsonSerialize()['players_to_reach']);
        $this->assertEquals(1, $result[1]->jsonSerialize()['players_reached']);
        $this->assertEquals(0, $result[1]->jsonSerialize()['requests']);
        $this->assertEquals($team->tag, $result[1]->jsonSerialize()['tag']);
        $this->assertEquals($tournament->name, $result[1]->jsonSerialize()['tournament_name']);
    }

    public function testCreateRequestNoTeam(): void
    {
        $this->team->delete();
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals([], $result->jsonSerialize());
    }

    public function testCreateRequestNoTournament(): void
    {
        $this->team->delete();
        $this->tournament->delete();
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals([], $result->jsonSerialize());
    }

    public function testCreateRequestNoTags(): void
    {
        $this->tag->delete();
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->teamService->getUserTeams($request);

        $this->assertEquals([], $result->jsonSerialize());
    }
}

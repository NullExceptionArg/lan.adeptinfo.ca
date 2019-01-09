<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
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

<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
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

        $this->be($this->user);
    }

    public function testCreate(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->createRequest($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['team_id'], $result->team_id);
        $this->assertEquals($this->requestContent['tag_id'], $result->tag_id);
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

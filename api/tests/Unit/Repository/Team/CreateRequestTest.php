<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

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
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

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

    public function testCreateRequest(): void
    {
        $this->notSeeInDatabase('request', [
            'id' => 1,
            'team_id' => $this->requestContent['team_id'],
            'tag_id' => $this->requestContent['tag_id'],
        ]);

        $result = $this->teamRepository->createRequest(
            $this->requestContent['team_id'],
            $this->requestContent['tag_id']
        );

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['team_id'], $result->team_id);
        $this->assertEquals($this->requestContent['tag_id'], $result->tag_id);
        $this->seeInDatabase('request', [
            'id' => 1,
            'team_id' => $this->requestContent['team_id'],
            'tag_id' => $this->requestContent['tag_id']
        ]);

    }
}

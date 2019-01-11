<?php

namespace Tests\Unit\Repository\Team;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteRequestTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamRepository;

    protected $teamService;

    protected $user;
    protected $tag;
    protected $lan;
    protected $tournament;
    protected $team;
    protected $request;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = $this->app->make('App\Repositories\Implementation\TeamRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $this->lan = factory('App\Model\Lan')->create();
        $startTime = Carbon::parse($this->lan->lan_start);
        $endTime = Carbon::parse($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);
        $this->team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);
        $this->request = factory('App\Model\Request')->create([
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id,
        ]);
    }

    public function testDeleteRequest(): void
    {
        $this->seeInDatabase('request', [
            'id' => $this->request->id,
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id
        ]);

        $this->teamRepository->deleteRequest($this->request->id);

        $this->notSeeInDatabase('request', [
            'id' => $this->request->id,
            'tag_id' => $this->tag->id,
            'team_id' => $this->team->id
        ]);
    }
}

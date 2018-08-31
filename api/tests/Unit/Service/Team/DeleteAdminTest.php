<?php

namespace Tests\Unit\Service\Team;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteAdminTest extends TestCase
{
    use DatabaseMigrations;

    protected $teamService;

    protected $organizer;
    protected $lan;
    protected $tournament;
    protected $team;

    protected $requestContent = [
        'team_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->teamService = $this->app->make('App\Services\Implementation\TeamServiceImpl');

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
        $this->organizer = factory('App\Model\User')->create();
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->organizer->id,
            'tournament_id' => $this->tournament->id
        ]);

        $this->requestContent['team_id'] = $this->team->id;
        $this->be($this->organizer);
    }

    public function testDeleteAdmin(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->teamService->deleteAdmin($request);

        $this->assertEquals($this->team->id, $result->id);
        $this->assertEquals($this->team->name, $result->name);
        $this->assertEquals($this->team->tag, $result->tag);
        $this->assertEquals($this->team->tournament_id, $result->tournament_id);
    }

    public function testDeleteAdminTeamIdInteger(): void
    {
        $this->requestContent['team_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteAdmin($request);
            $this->fail('Expected: {"team_id":["The team id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The team id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteAdminTeamIdExist(): void
    {
        $this->requestContent['team_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteAdmin($request);
            $this->fail('Expected: {"team_id":["The selected team id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The selected team id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteAdminTeamIdUserIsTournamentAdmin(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->requestContent);
        try {
            $this->teamService->deleteAdmin($request);
            $this->fail('Expected: {"team_id":["The user doesn\'t have any tournaments"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"team_id":["The user doesn\'t have any tournaments"]}', $e->getMessage());
        }
    }
}

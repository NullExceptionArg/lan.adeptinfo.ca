<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class EditTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $user;
    protected $lan;
    protected $tournament;

    protected $requestContent = [
        'tournament_id' => null,
        'name' => 'October',
        'state' => 'visible',
        'tournament_start' => null,
        'tournament_end' => null,
        'players_to_reach' => 5,
        'teams_to_reach' => 6,
        'rules' => 'The Bolsheviks seize control of Petrograd.',
        'price' => 0,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(1)->format('Y-m-d H:i:s');
        $endTime = new Carbon($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour(1)->format('Y-m-d H:i:s');
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1)
        ]);

        $this->be($this->user);
    }

    public function testEdit(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->tournamentService->edit($request, $this->tournament->id);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['state'], $result->state);
        $this->assertEquals($this->requestContent['lan_id'], $result->lan_id);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->requestContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->requestContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->requestContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->requestContent['rules'], $result->rules);
        $this->assertEquals($this->requestContent['price'], $result->price);
    }

    public function testEditTournamentIdInteger(): void
    {
        $badTournamentId = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $badTournamentId);
            $this->fail('Expected: {"tournament_id":["The tournament id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The tournament id must be an integer."]}', $e->getMessage());
        }
    }

    public function testEditTournamentIdExist(): void
    {
        $badTournamentId = -1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $badTournamentId);
            $this->fail('Expected: {"tournament_id":["The selected tournament id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The selected tournament id is invalid."]}', $e->getMessage());
        }
    }

    public function testEditNameString(): void
    {
        $this->requestContent['name'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testEditNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"name":["The name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testEditStateInEnum(): void
    {
        $this->requestContent['state'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"state":["The selected state is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"state":["The selected state is invalid."]}', $e->getMessage());
        }
    }

    public function testEditPriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"price":["The price must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be an integer."]}', $e->getMessage());
        }
    }

    public function testEditPriceMin(): void
    {
        $this->requestContent['price'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"price":["The price must be at least 0."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be at least 0."]}', $e->getMessage());
        }
    }

    public function testEditTournamentStartAfterOrEqualLanStartTime(): void
    {

        $startTime = new Carbon($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->subHour(1)->format('Y-m-d H:i:s');
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"tournament_start":["The tournament start time must be after or equal the lan start time."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_start":["The tournament start time must be after or equal the lan start time."]}', $e->getMessage());
        }
    }

    public function testEditTournamentEndBeforeOrEqualLanEndTime(): void
    {
        $endTime = new Carbon($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->addHour(1)->format('Y-m-d H:i:s');
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"tournament_end":["The tournament end time must be before or equal the lan end time."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_end":["The tournament end time must be before or equal the lan end time."]}', $e->getMessage());
        }
    }

    public function testEditPlayersToReachMin(): void
    {
        $this->requestContent['players_to_reach'] = 0;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"players_to_reach":["The players to reach must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach must be at least 1."]}', $e->getMessage());
        }
    }

    public function testEditPlayersToReachInteger(): void
    {
        $this->requestContent['players_to_reach'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"players_to_reach":["The players to reach must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach must be an integer."]}', $e->getMessage());
        }
    }

    public function testEditPlayersToReachLock(): void
    {
        $tag = factory('App\Model\Tag')->create([
            'user_id' => $this->user->id
        ]);
        $team = factory('App\Model\Team')->create([
            'tournament_id' => $this->tournament->id
        ]);
        factory('App\Model\TagTeam')->create([
            'tag_id' => $tag->id,
            'team_id' => $team->id
        ]);
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"players_to_reach":["The players to reach can\'t be changed once users have started registering for the tournament."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach can\'t be changed once users have started registering for the tournament."]}', $e->getMessage());
        }
    }

    public function testEditTeamsToReachMin(): void
    {
        $this->requestContent['teams_to_reach'] = 0;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"teams_to_reach":["The teams to reach must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"teams_to_reach":["The teams to reach must be at least 1."]}', $e->getMessage());
        }
    }

    public function testEditTeamsToReachInteger(): void
    {
        $this->requestContent['teams_to_reach'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"teams_to_reach":["The teams to reach must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"teams_to_reach":["The teams to reach must be an integer."]}', $e->getMessage());
        }
    }

    public function testEditRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->edit($request, $this->tournament->id);
            $this->fail('Expected: {"rules":["The rules must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"rules":["The rules must be a string."]}', $e->getMessage());
        }
    }
}

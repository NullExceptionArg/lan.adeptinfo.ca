<?php

namespace Tests\Unit\Service\Tournament;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'name' => 'October',
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
        $this->tournamentService =  $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;
        $startTime = new Carbon($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(1)->format('Y-m-d H:i:s');
        $endTime = new Carbon($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour(1)->format('Y-m-d H:i:s');

        $this->be($this->user);
    }

    public function testCreate(): void
    {
        $request = new Request($this->requestContent);
        $result = $this->tournamentService->create($request);

        $this->assertEquals(1, $result->id);
        $this->assertEquals($this->requestContent['lan_id'], $result->lan_id);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->requestContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->requestContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->requestContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->requestContent['rules'], $result->rules);
        $this->assertEquals($this->requestContent['price'], $result->price);
    }

    public function testCreateCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $startTime = new Carbon($lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->addHour(1)->format('Y-m-d H:i:s');
        $endTime = new Carbon($lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->subHour(1)->format('Y-m-d H:i:s');
        $this->requestContent['lan_id'] = null;

        $request = new Request($this->requestContent);
        $result = $this->tournamentService->create($request);

        $this->assertEquals($lan->id, $result->lan_id);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->requestContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->requestContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->requestContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->requestContent['rules'], $result->rules);
        $this->assertEquals($this->requestContent['price'], $result->price);
    }

    public function testCreateLanIdInteger(): void
    {
        $this->requestContent['lan_id'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanIdExist(): void
    {
        $this->requestContent['lan_id'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateNameRequired(): void
    {
        $this->requestContent['name'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
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
            $this->tournamentService->create($request);
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
            $this->tournamentService->create($request);
            $this->fail('Expected: {"name":["The name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreatePriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"price":["The price must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreatePriceMin(): void
    {
        $this->requestContent['price'] = -1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"price":["The price must be at least 0."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be at least 0."]}', $e->getMessage());
        }
    }

    public function testCreatePriceDefault(): void
    {
        $this->requestContent['price'] = '';
        $request = new Request($this->requestContent);

        $result = $this->tournamentService->create($request);

        $this->assertEquals($this->lan->id, $result->lan_id);
        $this->assertEquals($this->requestContent['name'], $result->name);
        $this->assertEquals($this->requestContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->requestContent['tournament_end'], $result->tournament_end);
        $this->assertEquals($this->requestContent['players_to_reach'], $result->players_to_reach);
        $this->assertEquals($this->requestContent['teams_to_reach'], $result->teams_to_reach);
        $this->assertEquals($this->requestContent['rules'], $result->rules);
        $this->assertEquals(0, $result->price);
    }

    public function testCreateTournamentStartRequired(): void
    {
        $this->requestContent['tournament_start'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"tournament_start":["The tournament start field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_start":["The tournament start field is required."]}', $e->getMessage());
        }
    }

    public function testCreateTournamentStartAfterOrEqualLanStartTime(): void
    {
        $startTime = new Carbon($this->lan->lan_start);
        $this->requestContent['tournament_start'] = $startTime->subHour(1)->format('Y-m-d H:i:s');
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"tournament_start":["The tournament start time must be after or equal the lan start time."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_start":["The tournament start time must be after or equal the lan start time."]}', $e->getMessage());
        }
    }

    public function testCreateTournamentEndRequired(): void
    {
        $this->requestContent['tournament_end'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"tournament_end":["The tournament end field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_end":["The tournament end field is required."]}', $e->getMessage());
        }
    }

    public function testCreateTournamentEndBeforeOrEqualLanEndTime(): void
    {
        $endTime = new Carbon($this->lan->lan_end);
        $this->requestContent['tournament_end'] = $endTime->addHour(1)->format('Y-m-d H:i:s');
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"tournament_end":["The tournament end time must be before or equal the lan end time."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_end":["The tournament end time must be before or equal the lan end time."]}', $e->getMessage());
        }
    }

    public function testCreatePlayersToReachRequired(): void
    {
        $this->requestContent['players_to_reach'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"players_to_reach":["The players to reach field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach field is required."]}', $e->getMessage());
        }
    }

    public function testCreatePlayersToReachMin(): void
    {
        $this->requestContent['players_to_reach'] = 0;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"players_to_reach":["The players to reach must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach must be at least 1."]}', $e->getMessage());
        }
    }

    public function testCreatePlayersToReachInteger(): void
    {
        $this->requestContent['players_to_reach'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"players_to_reach":["The players to reach must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"players_to_reach":["The players to reach must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateTeamsToReachRequired(): void
    {
        $this->requestContent['teams_to_reach'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"teams_to_reach":["The teams to reach field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"teams_to_reach":["The teams to reach field is required."]}', $e->getMessage());
        }
    }

    public function testCreateTeamsToReachMin(): void
    {
        $this->requestContent['teams_to_reach'] = 0;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"teams_to_reach":["The teams to reach must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"teams_to_reach":["The teams to reach must be at least 1."]}', $e->getMessage());
        }
    }

    public function testCreateTeamsToReachInteger(): void
    {
        $this->requestContent['teams_to_reach'] = '☭';
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"teams_to_reach":["The teams to reach must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"teams_to_reach":["The teams to reach must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateRulesRequired(): void
    {
        $this->requestContent['rules'] = null;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"rules":["The rules field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"rules":["The rules field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $request = new Request($this->requestContent);
        try {
            $this->tournamentService->create($request);
            $this->fail('Expected: {"rules":["The rules must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"rules":["The rules must be a string."]}', $e->getMessage());
        }
    }
}

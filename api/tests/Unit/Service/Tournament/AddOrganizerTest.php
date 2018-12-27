<?php

namespace Tests\Unit\Service\Tournament;

use App\Model\Permission;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class AddOrganizerTest extends TestCase
{
    use DatabaseMigrations;

    protected $tournamentService;

    protected $organizer;
    protected $organizer2;
    protected $lan;
    protected $tournament;

    public function setUp(): void
    {
        parent::setUp();
        $this->tournamentService = $this->app->make('App\Services\Implementation\TournamentServiceImpl');

        $this->organizer = factory('App\Model\User')->create();
        $this->organizer2 = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $startTime = new Carbon($this->lan->lan_start);
        $endTime = new Carbon($this->lan->lan_end);
        $this->tournament = factory('App\Model\Tournament')->create([
            'lan_id' => $this->lan->id,
            'tournament_start' => $startTime->addHour(1),
            'tournament_end' => $endTime->subHour(1),
            'teams_to_reach' => 10,
            'players_to_reach' => 10
        ]);
        factory('App\Model\OrganizerTournament')->create([
            'organizer_id' => $this->organizer->id,
            'tournament_id' => $this->tournament->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'add-organizer')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->organizer->id
        ]);

        $this->be($this->organizer);
    }

    public function testAddOrganizerHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request(['email' => $this->organizer2->email]);
        try {
            $this->tournamentService->addOrganizer($request, $this->tournament->id);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testAddOrganizer(): void
    {
        $request = new Request(['email' => $this->organizer2->email]);
        $result = $this->tournamentService->addOrganizer($request, $this->tournament->id);

        $this->assertEquals($this->tournament->id, $result->id);
        $this->assertEquals($this->tournament->lan_id, $result->lan_id);
        $this->assertEquals($this->tournament->name, $result->name);
        $this->assertEquals($this->tournament->price, $result->price);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_start)), $result->tournament_start);
        $this->assertEquals(date('Y-m-d H:i:s', strtotime($this->tournament->tournament_end)), $result->tournament_end);
        $this->assertEquals($this->tournament->players_to_reach, $result->players_to_reach);
        $this->assertEquals($this->tournament->teams_to_reach, $result->teams_to_reach);
        $this->assertEquals($this->tournament->rules, $result->rules);
    }

    public function testAddOrganizerTournamentIdExist(): void
    {
        $request = new Request(['email' => $this->organizer2->email]);;
        try {
            $this->tournamentService->addOrganizer($request, -1);
            $this->fail('Expected: {"tournament_id":["The selected tournament id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The selected tournament id is invalid."]}', $e->getMessage());
        }
    }

    public function testAddOrganizerOrganizerHasTournament(): void
    {
        $user = factory('App\Model\User')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'add-organizer')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $user->id
        ]);
        $this->be($user);
        $request = new Request(['email' => $this->organizer2->email]);
        try {
            $this->tournamentService->addOrganizer($request, $this->tournament->id);
            $this->fail('Expected: {"tournament_id":["The user doesn\'t have any tournaments"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_id":["The user doesn\'t have any tournaments"]}', $e->getMessage());
        }
    }

    public function testAddOrganizerEmailString(): void
    {
        $request = new Request(['email' => 0]);
        try {
            $this->tournamentService->addOrganizer($request, $this->tournament->id);
            $this->fail('Expected: {"email":["The email must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email must be a string."]}', $e->getMessage());
        }
    }

    public function testAddOrganizerEmailExist(): void
    {
        $request = new Request(['email' => '☭']);
        try {
            $this->tournamentService->addOrganizer($request, $this->tournament->id);
            $this->fail('Expected: {"email":["The selected email is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The selected email is invalid."]}', $e->getMessage());
        }
    }
}

<?php

namespace Tests\Unit\Service\Seat;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class ConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $permission = Permission::where('name', 'confirm-arrival')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testConfirmArrival(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->seatService->confirmArrival($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testConfirmArrivalHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->confirmArrival($request, env('SEAT_ID'));
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }


    public function testConfirmArrivalCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);

        $permission = Permission::where('name', 'confirm-arrival')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $request = new Request([
            'lan_id' => $lan->id
        ]);
        $result = $this->seatService->confirmArrival($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($lan->id, $result->lan_id);
    }

    public function testConfirmArrivalLanIdExist(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->seatService->confirmArrival($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->seatService->confirmArrival($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdExist(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $badSeatId = -1;
        try {
            $this->seatService->confirmArrival($request, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdFree(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'free');
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->confirmArrival($request, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is not associated with a reservation."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is not associated with a reservation."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdArrived(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'arrived');
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->confirmArrival($request, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already set to \'arrived\'"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already set to arrived."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdUnknown(): void
    {
        $badSeatId = "B4D-1D";
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->confirmArrival($request, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}', $e->getMessage());
        }
    }
}

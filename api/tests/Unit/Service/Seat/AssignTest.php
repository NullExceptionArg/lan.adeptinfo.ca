<?php

namespace Tests\Unit\Service\Seat;

use App\Model\Reservation;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class AssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $admin;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'seat_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->be($this->admin);
    }

    public function testAssignSeat(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);
        $result = $this->seatService->assign($request);

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testBookLanIdExist()
    {
        $request = new Request([
            'lan_id' => -1,
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);
        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testAssignSeatIdExist()
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => '☭',
            'user_email' => $this->user->email
        ]);
        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }

    public function testAssignSeatAvailable()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->book($this->lan->event_key, [env('SEAT_ID')]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);

        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"seat_id":["This seat is already taken for this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already taken for this event."]}', $e->getMessage());
        }
    }

    public function testAssignSeatUniqueUserInLan()
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = env('SEAT_ID_2');
        $reservation->save();
        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);

        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"lan_id":["The user already has a seat at this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The user already has a seat at this event."]}', $e->getMessage());
        }
    }

    public function testAssignSeatOnceInLan()
    {
        $otherUser = factory('App\Model\User')->create();
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = env('SEAT_ID');
        $reservation->save();

        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);

        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"seat_id":["This seat is already taken for this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already taken for this event."]}', $e->getMessage());
        }
    }

    public function testAssignSeatLanIdInteger()
    {
        $request = new Request([
            'lan_id' => '☭',
            'seat_id' => env('SEAT_ID'),
            'user_email' => $this->user->email
        ]);

        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testAssignSeatEmailExists()
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'seat_id' => env('SEAT_ID'),
            'user_email' => '☭'
        ]);

        try {
            $this->seatService->assign($request);
            $this->fail('Expected: {"user_email":["The selected user email is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_email":["The selected user email is invalid."]}', $e->getMessage());
        }
    }

}

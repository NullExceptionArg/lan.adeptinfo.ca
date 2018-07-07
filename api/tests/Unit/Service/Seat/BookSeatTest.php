<?php

namespace Tests\Unit\Service\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class BookSeatTest extends SeatsTestCase
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
        $this->be($this->user);
    }

    public function testBookSeat(): void
    {
        $result = $this->seatService->book($this->lan->id, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testBookLanIdExist(): void
    {
        $badLanId = -1;
        try {
            $this->seatService->book($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testBookSeatIdExist(): void
    {
        $badSeatId = '-1';
        try {
            $this->seatService->book($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }

    public function testBookSeatAvailable(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key_id);
        $seatsClient->events()->book($this->lan->event_key_id, [env('SEAT_ID')]);

        try {
            $this->seatService->book($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already taken for this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already taken for this event."]}', $e->getMessage());
        }
    }

    public function testBookSeatUniqueUserInLan(): void
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = env('SEAT_ID_2');
        $reservation->save();

        try {
            $this->seatService->book($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The user already has a seat at this even.t"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The user already has a seat at this event."]}', $e->getMessage());
        }
    }

    public function testBookSeatOnceInLan(): void
    {
        $otherUser = factory('App\Model\User')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = env('SEAT_ID');
        $reservation->save();

        try {
            $this->seatService->book($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already taken for this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already taken for this event."]}', $e->getMessage());
        }
    }

    public function testBookSeatLanIdInteger(): void
    {
        $badLanId = '☭';
        try {
            $this->seatService->book($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

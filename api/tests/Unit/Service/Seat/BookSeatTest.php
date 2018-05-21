<?php

namespace Tests\Unit\Service\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class BookSeatTest extends SeatsTestCase
{
    protected $seatService;

    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $paramsContent = [
        'seat_id' => "A-1"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testBookSeat()
    {
        $this->be($this->user);
        $result = $this->seatService->book($this->lan->id, $this->paramsContent['seat_id']);

        $this->assertEquals($this->paramsContent['seat_id'], $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testBookLanIdExist()
    {
        $this->be($this->user);
        $badLanId = -1;
        try {
            $this->seatService->book($badLanId, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["Lan with id ' . $badLanId . ' doesn\'t exist"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testBookSeatIdExist()
    {
        $this->be($this->user);
        $badSeatId = '-1';
        try {
            $this->seatService->book($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $badSeatId . ' doesn\'t exist in this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $badSeatId . ' doesn\'t exist in this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatAvailable()
    {
        $this->be($this->user);

        $seatsClient = new SeatsioClient($this->lan->secret_key_id);
        $seatsClient->events()->book($this->lan->event_key_id, [$this->paramsContent['seat_id']]);

        try {
            $this->seatService->book($this->lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatUniqueUserInLan()
    {
        $this->be($this->user);

        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = $this->paramsContent['seat_id'];
        $reservation->save();

        try {
            $this->seatService->book($this->lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["The user already has a seat at this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The user already has a seat at this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatOnceInLan()
    {
        $this->be($this->user);

        $otherUser = factory('App\Model\User')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = $this->paramsContent['seat_id'];
        $reservation->save();

        try {
            $this->seatService->book($this->lan->id, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["Seat with id ' . $this->paramsContent['seat_id'] . ' is already taken for this event"]}', $e->getMessage());
        }
    }

    public function testBookSeatLanIdInteger()
    {
        $badLanId = '☭';
        try {
            $this->seatService->book($badLanId, $this->paramsContent['seat_id']);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

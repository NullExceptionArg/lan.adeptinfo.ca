<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class UnConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $lan;
    protected $reservation;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->reservation = factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'arrived');

    }

    public function testUnConfirmArrival(): void
    {
        $result = $this->seatService->unConfirmArrival($this->lan->id, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testUnConfirmArrivalLanIdExist(): void
    {
        $badLanId = -1;
        try {
            $this->seatService->unConfirmArrival($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testUnConfirmArrivalLanIdInteger(): void
    {
        $badLanId = '☭';
        try {
            $this->seatService->unConfirmArrival($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testUnConfirmArrivalSeatIdExist(): void
    {
        $badSeatId = -1;
        try {
            $this->seatService->unConfirmArrival($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }

    public function testUnConfirmArrivalSeatIdFree(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'free');

        try {
            $this->seatService->unConfirmArrival($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is not associated with a reservation."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is not associated with a reservation."]}', $e->getMessage());
        }

    }

    public function testUnConfirmArrivalSeatIdBooked(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'booked');

        try {
            $this->seatService->unConfirmArrival($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already set to booked."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already set to booked."]}', $e->getMessage());
        }
    }

    public function testUnConfirmArrivalSeatIdUnknown(): void
    {
        $badSeatId = "B4D-1D";
        try {
            $this->seatService->unConfirmArrival($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }
}

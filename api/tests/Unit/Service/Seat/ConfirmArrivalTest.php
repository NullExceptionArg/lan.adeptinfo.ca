<?php

namespace Tests\Unit\Service\Seat;

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
    }

    public function testConfirmArrival(): void
    {
        $result = $this->seatService->confirmArrival($this->lan->id, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testConfirmArrivalLanIdExist(): void
    {
        $badLanId = -1;
        try {
            $this->seatService->confirmArrival($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalLanIdInteger(): void
    {
        $badLanId = '☭';
        try {
            $this->seatService->confirmArrival($badLanId, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdExist(): void
    {
        $badSeatId = -1;
        try {
            $this->seatService->confirmArrival($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdFree(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'free');

        try {
            $this->seatService->confirmArrival($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is not associated with a reservation."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is not associated with a reservation."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdArrived(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key, [env('SEAT_ID')], 'arrived');

        try {
            $this->seatService->confirmArrival($this->lan->id, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already set to \'arrived\'"]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already set to arrived."]}', $e->getMessage());
        }
    }

    public function testConfirmArrivalSeatIdUnknown(): void
    {
        $badSeatId = "B4D-1D";
        try {
            $this->seatService->confirmArrival($this->lan->id, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }
}

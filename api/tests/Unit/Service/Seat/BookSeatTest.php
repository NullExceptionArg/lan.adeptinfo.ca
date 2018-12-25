<?php

namespace Tests\Unit\Service\Seat;

use App\Model\Reservation;
use Illuminate\Http\Request;
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
        $request = new Request([
           'lan_id' => $this->lan->id
        ]);
        $result = $this->seatService->book($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testBookSeatCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $request = new Request([
           'lan_id' => $lan->id
        ]);
        $result = $this->seatService->book($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($lan->id, $result->lan_id);
    }

    public function testBookLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->seatService->book($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testBookSeatIdExist(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $badSeatId = '-1';
        try {
            $this->seatService->book($request, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid."]}', $e->getMessage());
        }
    }

    public function testBookSeatAvailable(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->book($this->lan->event_key, [env('SEAT_ID')]);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);

        try {
            $this->seatService->book($request, env('SEAT_ID'));
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
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);

        try {
            $this->seatService->book($request, env('SEAT_ID'));
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
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);

        try {
            $this->seatService->book($request, env('SEAT_ID'));
            $this->fail('Expected: {"seat_id":["This seat is already taken for this event."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["This seat is already taken for this event."]}', $e->getMessage());
        }
    }

    public function testBookSeatLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' =>  '☭'
        ]);
        try {
            $this->seatService->book($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

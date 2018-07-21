<?php

namespace Tests\Unit\Service\Seat;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class UnBookTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $seatService;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->be($this->user);
    }

    public function testUnBookSeat(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
        ]);
        $result = $this->seatService->unBook($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testUnBookSeatCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan
        ]);
        $request = new Request();
        $result = $this->seatService->unBook($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($lan->id, $result->lan_id);
    }

    public function testUnBookLanIdExist()
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unBook($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testUnBookSeatIdExist()
    {
        $badSeatId = 'a';
        $request = new Request([
            'lan_id' => $this->lan->id,
            'user_email' => $this->user->email
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unBook($request, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}', $e->getMessage());
        }
    }

    public function testUnBookSeatLanIdInteger()
    {
        $badLanId = '☭';
        $request = new Request([
            'lan_id' => $badLanId,
            'user_email' => $this->user->email
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unBook($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

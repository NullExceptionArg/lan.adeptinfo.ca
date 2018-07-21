<?php

namespace Tests\Unit\Service\Seat;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\SeatsTestCase;

class UnAssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $admin;
    protected $lan;
    protected $seatService;

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
    }

    public function testUnAssign(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'user_email' => $this->user->email
        ]);
        $result = $this->seatService->unAssign($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }

    public function testUnAssignCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan->id
        ]);
        $request = new Request([
            'user_email' => $this->user->email
        ]);
        $result = $this->seatService->unAssign($request, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($lan->id, $result->lan_id);
    }

    public function testBookLanIdExist()
    {
        $request = new Request([
            'lan_id' => -1,
            'user_email' => $this->user->email
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unAssign($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testUnAssignSeatIdExist()
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
            $this->seatService->unAssign($request, $badSeatId);
            $this->fail('Expected: {"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_id":["The selected seat id is invalid.","The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist."]}', $e->getMessage());
        }
    }

    public function testUnAssignLanIdInteger()
    {
        $request = new Request([
            'lan_id' => '☭',
            'user_email' => $this->user->email
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unAssign($request, env('SEAT_ID'));
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testUnAssignEmailExists()
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'user_email' => '☭'
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->seatService->unAssign($request, env('SEAT_ID'));
            $this->fail('Expected: {"user_email":["The selected user email is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"user_email":["The selected user email is invalid."]}', $e->getMessage());
        }
    }
}

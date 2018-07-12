<?php

namespace Tests\Unit\Service;

use DateTime;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetUserDetailsTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $lan;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
    }

    public function testGetUserDetailsHasLanId(): void
    {
        $request = new Request([
            'lan_id' => $this->lan->id,
            'email' => $this->user->email
        ]);
        $result = $this->userService->getUserDetails($request)->jsonSerialize();
        $placeHistory = $result['place_history']->jsonSerialize();

        $this->assertEquals($this->user->getFullName(), $result['full_name']);
        $this->assertEquals($this->user->email, $result['email']);
        $this->assertEquals(null, $result['current_place']);
        $this->assertEquals([], $placeHistory);
    }

    public function testGetUserDetailsReservedAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'email' => $this->user->email
        ]);
        $result = $this->userService->getUserDetails($request)->jsonSerialize();
        $placeHistory = $result['place_history']->jsonSerialize();

        $this->assertEquals($this->user->getFullName(), $result['full_name']);
        $this->assertEquals($this->user->email, $result['email']);
        $this->assertEquals($reservation->seat_id, $result['current_place']);
        $this->assertEquals($this->lan->name, $placeHistory[0]['lan']);
        $this->assertEquals($reservation->seat_id, $placeHistory[0]['seat_id']);
        $this->assertEquals(null, $placeHistory[0]['arrived_at']);
        $this->assertEquals(null, $placeHistory[0]['canceled_at']);
        $this->assertEquals($reservation->created_at, $placeHistory[0]['reserved_at']);
        $this->assertEquals(null, $placeHistory[0]['left_at']);
    }

    public function testGetUserDetailsArrivedAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime()
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'email' => $this->user->email
        ]);
        $result = $this->userService->getUserDetails($request)->jsonSerialize();
        $placeHistory = $result['place_history']->jsonSerialize();

        $this->assertEquals($this->user->getFullName(), $result['full_name']);
        $this->assertEquals($this->user->email, $result['email']);
        $this->assertEquals($reservation->seat_id, $result['current_place']);
        $this->assertEquals($this->lan->name, $placeHistory[0]['lan']);
        $this->assertEquals($reservation->seat_id, $placeHistory[0]['seat_id']);
        $this->assertEquals($reservation->arrived_at->format('Y-m-d H:i:s'), $placeHistory[0]['arrived_at']);
        $this->assertEquals(null, $placeHistory[0]['canceled_at']);
        $this->assertEquals($reservation->created_at, $placeHistory[0]['reserved_at']);
        $this->assertEquals(null, $placeHistory[0]['left_at']);
    }

    public function testGetUserDetailsLeftAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime(),
            'left_at' => new DateTime()
        ]);
        $request = new Request([
            'lan_id' => $this->lan->id,
            'email' => $this->user->email
        ]);
        $result = $this->userService->getUserDetails($request)->jsonSerialize();
        $placeHistory = $result['place_history']->jsonSerialize();

        $this->assertEquals($this->user->getFullName(), $result['full_name']);
        $this->assertEquals($this->user->email, $result['email']);
        $this->assertEquals($reservation->seat_id, $result['current_place']);
        $this->assertEquals($this->lan->name, $placeHistory[0]['lan']);
        $this->assertEquals($reservation->seat_id, $placeHistory[0]['seat_id']);
        $this->assertEquals($reservation->arrived_at->format('Y-m-d H:i:s'), $placeHistory[0]['arrived_at']);
        $this->assertEquals(null, $placeHistory[0]['canceled_at']);
        $this->assertEquals($reservation->created_at, $placeHistory[0]['reserved_at']);
        $this->assertEquals($reservation->left_at->format('Y-m-d H:i:s'), $placeHistory[0]['left_at']);
    }

    public function testGetUserDetailsCanceledAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime(),
            'left_at' => new DateTime(),
        ]);
        $reservation->delete();
        $request = new Request([
            'lan_id' => $this->lan->id,
            'email' => $this->user->email
        ]);
        $result = $this->userService->getUserDetails($request)->jsonSerialize();
        $placeHistory = $result['place_history']->jsonSerialize();

        $this->assertEquals($this->user->getFullName(), $result['full_name']);
        $this->assertEquals($this->user->email, $result['email']);
        $this->assertEquals(null, $result['current_place']);
        $this->assertEquals($this->lan->name, $placeHistory[0]['lan']);
        $this->assertEquals($reservation->seat_id, $placeHistory[0]['seat_id']);
        $this->assertEquals($reservation->arrived_at->format('Y-m-d H:i:s'), $placeHistory[0]['arrived_at']);
        $this->assertEquals($reservation->deleted_at->format('Y-m-d H:i:s'), $placeHistory[0]['canceled_at']);
        $this->assertEquals($reservation->created_at, $placeHistory[0]['reserved_at']);
        $this->assertEquals($reservation->left_at->format('Y-m-d H:i:s'), $placeHistory[0]['left_at']);
    }

    public function testGetUserDetailsLanIdRequired(): void
    {

    }

    public function testGetUserDetailsLanExist(): void
    {

    }

    public function testGetUserDetailsLanIdInteger(): void
    {

    }

    public function testGetUserDetailsEmailRequired(): void
    {

    }

    public function testGetUserDetailsEmailExist(): void
    {

    }

}


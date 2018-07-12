<?php

namespace Tests\Unit\Controller\User;

use DateTime;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetUserDetailsTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $lan;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();
    }

    public function testGetUserDetailsHasLanId(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'full_name' => $this->user->getFullName(),
                'email' => $this->user->email,
                'current_place' => null,
                'place_history' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserDetailsReservedAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'full_name' => $this->user->getFullName(),
                'email' => $this->user->email,
                'current_place' => $reservation->seat_id,
                'place_history' => [[
                    'lan' => $this->lan->name,
                    'seat_id' => $reservation->seat_id,
                    'arrived_at' => null,
                    'canceled_at' => null,
                    'left_at' => null,
                    'reserved_at' => $reservation->created_at->format('Y-m-d H:i:s')
                ]]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserDetailsArrivedAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime()
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'full_name' => $this->user->getFullName(),
                'email' => $this->user->email,
                'current_place' => $reservation->seat_id,
                'place_history' => [[
                    'lan' => $this->lan->name,
                    'seat_id' => $reservation->seat_id,
                    'arrived_at' => $reservation->arrived_at->format('Y-m-d H:i:s'),
                    'canceled_at' => null,
                    'left_at' => null,
                    'reserved_at' => $reservation->created_at->format('Y-m-d H:i:s')
                ]]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserDetailsLeftAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime(),
            'left_at' => new DateTime()
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'full_name' => $this->user->getFullName(),
                'email' => $this->user->email,
                'current_place' => $reservation->seat_id,
                'place_history' => [[
                    'lan' => $this->lan->name,
                    'seat_id' => $reservation->seat_id,
                    'arrived_at' => $reservation->arrived_at->format('Y-m-d H:i:s'),
                    'canceled_at' => null,
                    'left_at' => $reservation->left_at->format('Y-m-d H:i:s'),
                    'reserved_at' => $reservation->created_at->format('Y-m-d H:i:s')
                ]]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUserDetailsCanceledAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'arrived_at' => new DateTime(),
            'left_at' => new DateTime(),
            'deleted_at' => new DateTime()
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'full_name' => $this->user->getFullName(),
                'email' => $this->user->email,
                'current_place' => null,
                'place_history' => [[
                    'lan' => $this->lan->name,
                    'seat_id' => $reservation->seat_id,
                    'arrived_at' => $reservation->arrived_at->format('Y-m-d H:i:s'),
                    'canceled_at' => $reservation->deleted_at->format('Y-m-d H:i:s'),
                    'left_at' => $reservation->left_at->format('Y-m-d H:i:s'),
                    'reserved_at' => $reservation->created_at->format('Y-m-d H:i:s')
                ]]
            ])
            ->assertResponseStatus(200);
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

    public function testGetUserDetailsEmailExist(): void
    {

    }
}

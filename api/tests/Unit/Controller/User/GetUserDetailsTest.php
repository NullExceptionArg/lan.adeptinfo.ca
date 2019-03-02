<?php

namespace Tests\Unit\Controller\User;

use Carbon\Carbon;
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
        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'get-user-details'
        );
    }

    public function testGetUserDetailsHasLanId(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
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

    public function testGetUserDetailsHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
                'email' => $this->user->email,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testGetUserDetailsReservedAt(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
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
            'arrived_at' => Carbon::now()
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
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
            'arrived_at' => Carbon::now(),
            'left_at' => Carbon::now()
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
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
            'arrived_at' => Carbon::now(),
            'left_at' => Carbon::now(),
        ]);
        $reservation->delete();
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
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

    public function testGetUserDetailsLanExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
                'email' => $this->user->email,
                'lan_id' => -1
            ])->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUserDetailsLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
                'email' => $this->user->email,
                'lan_id' => '☭'
            ])->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUserDetailsEmailRequired(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The email field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUserDetailsEmailExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/user/details', [
                'email' => -1,
                'lan_id' => $this->lan->id
            ])->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'email' => [
                        0 => 'The selected email is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

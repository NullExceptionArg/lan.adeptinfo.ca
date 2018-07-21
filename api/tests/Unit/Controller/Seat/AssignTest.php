<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class AssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $admin;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'seat_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testAssignSeat(): void
    {
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(201);
    }

    public function testAssignSeatCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(201);
    }

    public function testBookLanIdExist()
    {
        $badLanId = -1;
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $badLanId,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
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

    public function testAssignSeatIdExist()
    {
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/'  . '☭', [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignSeatAvailable()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events()->book($this->lan->event_key, [env('SEAT_ID')]);

        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'This seat is already taken for this event.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignSeatUniqueUserInLan()
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = env('SEAT_ID_2');
        $reservation->save();

        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The user already has a seat at this event.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignSeatOnceInLan()
    {
        $otherUser = factory('App\Model\User')->create();
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = env('SEAT_ID');
        $reservation->save();

        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'This seat is already taken for this event.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignSeatLanIdInteger()
    {
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => '☭',
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testAssignSeatEmailExists()
    {
        $this->actingAs($this->admin)
            ->json('POST', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => '☭'
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'The selected user email is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

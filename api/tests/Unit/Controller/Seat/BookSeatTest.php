<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class BookSeatTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'seat_id' => "A-1"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testBookSeat()
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/book/' . $this->requestContent['seat_id'])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => $this->requestContent['seat_id']
            ])
            ->assertResponseStatus(201);
    }

    public function testBookLanIdExist()
    {
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/book/' . $this->requestContent['seat_id'])
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

    public function testBookSeatIdExist()
    {
        $badSeatId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/book/' . $badSeatId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'Seat with id ' . $badSeatId . ' doesn\'t exist in this event'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatAvailable()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key_id);
        $seatsClient->events()->book($this->lan->event_key_id, [$this->requestContent['seat_id']]);

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/book/' . $this->requestContent['seat_id'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'Seat with id ' . $this->requestContent['seat_id'] . ' is already taken for this event'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatUniqueUserInLan()
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = $this->requestContent['seat_id'];
        $reservation->save();

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/book/' . $this->requestContent['seat_id'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The user already has a seat at this event'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatOnceInLan()
    {
        $otherUser = factory('App\Model\User')->create();
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = $this->requestContent['seat_id'];
        $reservation->save();

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/book/' . $this->requestContent['seat_id'])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'Seat with id ' . $this->requestContent['seat_id'] . ' is already taken for this event'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatLanIdInteger()
    {
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/book/' . $this->requestContent['seat_id'])
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

}

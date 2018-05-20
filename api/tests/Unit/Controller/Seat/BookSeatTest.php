<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class BookSeatTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $requestContent = [
        'seat_id' => "A-1"
    ];


    public function testBookSeat()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/book/' . $this->requestContent['seat_id'])
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => $this->requestContent['seat_id']
            ])
            ->assertResponseStatus(201);
    }

    public function testBookLanIdExist()
    {
        $user = factory('App\Model\User')->create();
        $badLanId = -1;

        $this->actingAs($user)
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
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $badSeatId = '☭';

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/book/' . $badSeatId)
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
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $seatsClient = new SeatsioClient($lan->secret_key_id);
        $seatsClient->events()->book($lan->event_key_id, [$this->requestContent['seat_id']]);

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/book/' . $this->requestContent['seat_id'])
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
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $lan->id;
        $reservation->user_id = $user->id;
        $reservation->seat_id = $this->requestContent['seat_id'];
        $reservation->save();

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/book/' . $this->requestContent['seat_id'])
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
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $otherUser = factory('App\Model\User')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = $this->requestContent['seat_id'];
        $reservation->save();

        $this->actingAs($user)
            ->json('POST', '/api/lan/' . $lan->id . '/book/' . $this->requestContent['seat_id'])
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
        $user = factory('App\Model\User')->create();
        $badLanId = '☭';

        $this->actingAs($user)
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

<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class BookTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testBook(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_TEST_ID')
            ])
            ->assertResponseStatus(201);
    }

    public function testBookCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'))
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => env('SEAT_TEST_ID')
            ])
            ->assertResponseStatus(201);
    }

    public function testBookLanIdExist()
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => -1
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

    public function testBookIdExist()
    {
        $badSeatId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . $badSeatId, [
                'lan_id' => $this->lan->id
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

    public function testBookAvailable()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->book($this->lan->event_key, [env('SEAT_TEST_ID')]);

        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
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

    public function testBookUniqueUserInLan()
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = env('SEAT_TEST_ID_2');
        $reservation->save();

        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
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

    public function testBookOnceInLan()
    {
        $otherUser = factory('App\Model\User')->create();
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $otherUser->id;
        $reservation->seat_id = env('SEAT_TEST_ID');
        $reservation->save();

        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
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

    public function testBookLanIdInteger()
    {
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/seat/book/' . env('SEAT_TEST_ID'), [
                'lan_id' => $badLanId
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

}

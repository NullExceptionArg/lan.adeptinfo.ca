<?php

namespace Tests\Unit\Controller\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class ConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $reservation;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->reservation = factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
    }

    public function testConfirmArrival()
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . env('SEAT_ID'))
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testConfirmArrivalLanIdExist()
    {
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/confirm/' . env('SEAT_ID'))
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

    public function testConfirmArrivalLanIdInteger()
    {
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/confirm/' . env('SEAT_ID'))
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

    public function testConfirmArrivalSeatIdExist()
    {
        $badSeatId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . $badSeatId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdFree()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key_id);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key_id, [env('SEAT_ID')], 'free');

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . env('SEAT_ID'))
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'Seat with id ' . env('SEAT_ID') . ' is not associated with a reservation'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdArrived()
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key_id);
        $seatsClient->events()->changeObjectStatus($this->lan->event_key_id, [env('SEAT_ID')], 'arrived');

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . env('SEAT_ID'))
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "Seat with id " . env('SEAT_ID') . " is already set to 'arrived'"
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdUnknown()
    {
        $badSeatId = "B4D-1D";
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . $badSeatId)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "The selected seat id is invalid."
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

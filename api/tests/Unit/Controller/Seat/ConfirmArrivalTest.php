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

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->reservation = factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
    }

    public function testConfirmArrival(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/confirm/' . env('SEAT_ID'))
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testConfirmArrivalLanIdExist(): void
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

    public function testConfirmArrivalLanIdInteger(): void
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

    public function testConfirmArrivalSeatIdExist(): void
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

    public function testBookSeatIdFree(): void
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
                        0 => 'This seat is not associated with a reservation.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdArrived(): void
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
                        0 => "This seat is already set to arrived."
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdUnknown(): void
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

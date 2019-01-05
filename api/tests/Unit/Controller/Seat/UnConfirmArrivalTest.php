<?php

namespace Tests\Unit\Controller\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class UnConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'unconfirm-arrival'
        );
    }

    public function testUnConfirmArrival(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT__TEST_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnConfirmArrivalHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testUnConfirmArrivalCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $lan->id,
            'unconfirm-arrival'
        );

        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan,
            'seat_id' => env('SEAT__TEST_ID')
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => env('SEAT__TEST_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnConfirmArrivalLanIdExist(): void
    {
        $badLanId = -1;
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $badLanId
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUnConfirmArrivalLanIdInteger(): void
    {
        $badLanId = '☭';
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
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

    public function testUnConfirmArrivalSeatIdExist(): void
    {
        $badSeatId = -1;
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . $badSeatId, [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.',
                        1 => 'The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUnBookSeatIdFree(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'free');

        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
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
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . env('SEAT__TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "This seat is already set to booked."
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdUnknown(): void
    {
        $badSeatId = "B4D-1D";
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT__TEST_ID')], 'arrived');
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/confirm/' . $badSeatId, [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "The selected seat id is invalid.",
                        1 => 'The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

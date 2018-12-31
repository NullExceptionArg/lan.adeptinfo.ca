<?php

namespace Tests\Unit\Controller\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class UnBookTest extends SeatsTestCase
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

    public function testUnBook(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/book/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnBookCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/book/' . env('SEAT_ID'))
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnBookLanIdExist()
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/book/' . env('SEAT_ID'), [
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

    public function testUnBookIdExist()
    {
        $badSeatId = '☭';
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/book/' . $badSeatId, [
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

    public function testUnBookLanIdInteger()
    {
        $badLanId = '☭';
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/seat/book/' . env('SEAT_ID'), [
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

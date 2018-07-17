<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetLanSimple(): void
    {
        $this->json('GET', '/api/lan', [
            'lan_id' => $this->lan->id
        ])
            ->seeJsonEquals([
                'id' => $this->lan->id,
                'name' => $this->lan->name,
                'lan_start' => $this->lan->lan_start,
                'lan_end' => $this->lan->lan_end,
                'seat_reservation_start' => $this->lan->seat_reservation_start,
                'tournament_reservation_start' => $this->lan->tournament_reservation_start,
                'longitude' => $this->lan->longitude,
                'latitude' => $this->lan->latitude,
                'places' => [
                    'reserved' => 0,
                    'total' => $this->lan->places
                ],
                'secret_key' => $this->lan->secret_key,
                'event_key' => $this->lan->event_key,
                'public_key' => $this->lan->public_key,
                'price' => $this->lan->price,
                'rules' => $this->lan->rules,
                'description' => $this->lan->description,
                'images' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testGetCurrentLanHasCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->json('GET',  '/api/lan')
            ->seeJsonEquals([
                'id' => $lan->id,
                'name' => $lan->name,
                'lan_start' => $lan->lan_start,
                'lan_end' => $lan->lan_end,
                'seat_reservation_start' => $lan->seat_reservation_start,
                'tournament_reservation_start' => $lan->tournament_reservation_start,
                'longitude' => $lan->longitude,
                'latitude' => $lan->latitude,
                'secret_key' => $lan->secret_key,
                'event_key' => $lan->event_key,
                'public_key' => $lan->public_key,
                'places' => [
                    'reserved' => 0,
                    'total' => $lan->places
                ],
                'price' => $lan->price,
                'rules' => $lan->rules,
                'description' => $lan->description,
                'images' => []
            ])
            ->seeStatusCode(200);
    }

    public function testGetLanParameters(): void
    {
        $this->json('GET', '/api/lan', [
            'fields' => 'lan_start,lan_end,seat_reservation_start',
            'lan_id' => $this->lan->id
        ])
            ->seeJsonEquals([
                'id' => $this->lan->id,
                'lan_start' => $this->lan->lan_start,
                'lan_end' => $this->lan->lan_end,
                'seat_reservation_start' => $this->lan->seat_reservation_start
            ])
            ->assertResponseStatus(200);
    }

    public function testGetLanIdExist(): void
    {

        $this->json('GET', '/api/lan', [
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

    public function testGetLanIdInteger(): void
    {
        $this->json('GET', '/api/lan', [
            'lan_id' => '☭'
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

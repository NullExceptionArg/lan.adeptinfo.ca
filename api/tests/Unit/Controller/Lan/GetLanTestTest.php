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
        $this->json('GET', '/api/lan/' . $this->lan->id)
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

    public function testGetLanParameters(): void
    {
        $queryParams = ['fields' => "lan_start,lan_end,seat_reservation_start"];
        $this->json('GET', '/api/lan/' . $this->lan->id, $queryParams)
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
        $badLanId = -1;
        $this->json('GET', '/api/lan/' . $badLanId)
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
        $badLanId = '☭';
        $this->json('GET', '/api/lan/' . $badLanId)
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

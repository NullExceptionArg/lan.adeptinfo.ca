<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTest extends TestCase
{
    use DatabaseMigrations;

    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetSimple(): void
    {
        $this->json('GET', 'http://' . env('API_DOMAIN') . '/lan', [
            'lan_id' => $this->lan->id
        ])
            ->seeJsonEquals([
                'id' => $this->lan->id,
                'date' => $this->lan->getDateAttribute(),
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
                'event_key' => $this->lan->event_key,
                'price' => $this->lan->price,
                'rules' => $this->lan->rules,
                'description' => $this->lan->description,
                'images' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testGetCurrentLanCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->json('GET', 'http://' . env('API_DOMAIN') . '/lan')
            ->seeJsonEquals([
                'id' => $lan->id,
                'name' => $lan->name,
                'date' => $this->lan->getDateAttribute(),
                'lan_start' => $lan->lan_start,
                'lan_end' => $lan->lan_end,
                'seat_reservation_start' => $lan->seat_reservation_start,
                'tournament_reservation_start' => $lan->tournament_reservation_start,
                'longitude' => $lan->longitude,
                'latitude' => $lan->latitude,
                'event_key' => $lan->event_key,
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

    public function testGetParameters(): void
    {
        $this->json('GET', 'http://' . env('API_DOMAIN') . '/lan', [
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

    public function testGetIdExist(): void
    {

        $this->json('GET', 'http://' . env('API_DOMAIN') . '/lan', [
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

    public function testGetIdInteger(): void
    {
        $this->json('GET', 'http://' . env('API_DOMAIN') . '/lan', [
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

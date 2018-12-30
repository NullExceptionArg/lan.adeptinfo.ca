<?php

namespace Tests\Unit\Controller\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetCurrentTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'set-current-lan'
        );
    }

    public function testSetCurrentNoCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', ['lan_id' => $lan->id])
            ->seeJsonEquals([
                'name' => $lan->name,
                'lan_start' => $lan->lan_start,
                'lan_end' => $lan->lan_end,
                'seat_reservation_start' => $lan->seat_reservation_start,
                'tournament_reservation_start' => $lan->tournament_reservation_start,
                "event_key" => $lan->event_key,
                "public_key" => $lan->public_key,
                "secret_key" => $lan->secret_key,
                "latitude" => $lan->latitude,
                "longitude" => $lan->longitude,
                "places" => $lan->places,
                "price" => $lan->price,
                "rules" => $lan->rules,
                "description" => $lan->description,
                'is_current' => true,
                "id" => 1
            ])
            ->assertResponseStatus(200);
    }

    public function testSetCurrentHasPermission(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/lan/current', [
                'lan_id' => $lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testSetCurrentHasCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', ['lan_id' => $lan->id])
            ->seeJsonEquals([
                'name' => $lan->name,
                'lan_start' => $lan->lan_start,
                'lan_end' => $lan->lan_end,
                'seat_reservation_start' => $lan->seat_reservation_start,
                'tournament_reservation_start' => $lan->tournament_reservation_start,
                "event_key" => $lan->event_key,
                "public_key" => $lan->public_key,
                "secret_key" => $lan->secret_key,
                "latitude" => $lan->latitude,
                "longitude" => $lan->longitude,
                "places" => $lan->places,
                "price" => $lan->price,
                "rules" => $lan->rules,
                "description" => $lan->description,
                'is_current' => true,
                "id" => 1
            ])
            ->assertResponseStatus(200);
    }

    public function testSetCurrentIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', [
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

    public function testSetCurrentIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/current', [
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

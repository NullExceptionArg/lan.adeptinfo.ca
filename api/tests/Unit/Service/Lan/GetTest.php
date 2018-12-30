<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetSimple(): void
    {
        $result = $this->lanService->get($this->lan->id, null);

        $this->assertEquals($this->lan->id, $result['id']);
        $this->assertEquals($this->lan->name, $result['name']);
        $this->assertEquals($this->lan->lan_start, $result['lan_start']);
        $this->assertEquals($this->lan->lan_end, $result['lan_end']);
        $this->assertEquals($this->lan->seat_reservation_start, $result['seat_reservation_start']);
        $this->assertEquals($this->lan->tournament_reservation_start, $result['tournament_reservation_start']);
        $this->assertEquals($this->lan->latitude, $result['latitude']);
        $this->assertEquals($this->lan->longitude, $result['longitude']);
        $this->assertEquals($this->lan->secret_key, $result['secret_key']);
        $this->assertEquals($this->lan->event_key, $result['event_key']);
        $this->assertEquals($this->lan->public_key, $result['public_key']);
        $this->assertEquals($this->lan->price, $result['price']);
        $this->assertEquals($this->lan->rules, $result['rules']);
        $this->assertEquals($this->lan->description, $result['description']);
    }

    public function testGetParameters(): void
    {
        $result = $this->lanService->get(
            $this->lan->id,
            'lan_start,lan_start,lan_end,seat_reservation_start'
        );

        $this->assertEquals($this->lan->id, $result['id']);
        $this->assertEquals($this->lan->lan_start, $result['lan_start']);
        $this->assertEquals($this->lan->lan_end, $result['lan_end']);
        $this->assertEquals($this->lan->seat_reservation_start, $result['seat_reservation_start']);
    }
}

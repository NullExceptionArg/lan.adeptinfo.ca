<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetCurrentLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testSetCurrentLanHasCurrentLanSimple()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $result = $this->lanService->getCurrentLan();

        $this->assertEquals($lan->id, $result['id']);
        $this->assertEquals($lan->name, $result['name']);
        $this->assertEquals($lan->lan_start, $result['lan_start']);
        $this->assertEquals($lan->lan_end, $result['lan_end']);
        $this->assertEquals($lan->seat_reservation_start, $result['seat_reservation_start']);
        $this->assertEquals($lan->tournament_reservation_start, $result['tournament_reservation_start']);
        $this->assertEquals(number_format($lan->latitude, 7), $result['latitude']);
        $this->assertEquals(number_format($lan->longitude, 7), $result['longitude']);
        $this->assertEquals($lan->price, $result['price']);
        $this->assertEquals($lan->rules, $result['rules']);
        $this->assertEquals($lan->description, $result['description']);
    }

    public function testSetCurrentLanHasCurrentLanParameters()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $result = $this->lanService->getCurrentLan();

        $this->assertEquals($lan->id, $result['id']);
        $this->assertEquals($lan->lan_start, $result['lan_start']);
        $this->assertEquals($lan->lan_end, $result['lan_end']);
        $this->assertEquals($lan->seat_reservation_start, $result['seat_reservation_start']);
    }

    public function testSetCurrentLanNoCurrentLan()
    {
        factory('App\Model\Lan')->create();
        $result = $this->lanService->getCurrentLan();

        $this->assertEquals(null, $result);
    }
}

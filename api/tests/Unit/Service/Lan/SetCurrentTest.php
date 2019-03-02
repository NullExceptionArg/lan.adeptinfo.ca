<?php

namespace Tests\Unit\Service\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetCurrentTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;
    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testSetCurrentNoCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create();
        $result = $this->lanService->setCurrent($lan->id);

        $this->assertEquals($lan->name, $result->name);
        $this->assertEquals($lan->lan_start, $result->lan_start);
        $this->assertEquals($lan->lan_end, $result->lan_end);
        $this->assertEquals($lan->seat_reservation_start, $result->seat_reservation_start);
        $this->assertEquals($lan->tournament_reservation_start, $result->tournament_reservation_start);
        $this->assertEquals($lan->event_key, $result->event_key);
        $this->assertEquals($lan->public_key, $result->public_key);
        $this->assertEquals($lan->secret_key, $result->secret_key);
        $this->assertEquals($lan->latitude, $result->latitude);
        $this->assertEquals($lan->longitude, $result->longitude);
        $this->assertEquals($lan->places, $result->places);
        $this->assertEquals(true, $result->is_current);
        $this->assertEquals($lan->price, $result->price);
        $this->assertEquals($lan->rules, $result->rules);
        $this->assertEquals($lan->description, $result->description);
    }
}

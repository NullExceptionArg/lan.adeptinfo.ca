<?php

namespace Tests\Unit\Service\Lan;

use Carbon\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $lan;
    protected $user;

    protected $paramsContent = [
        'lan_id' => null,
        'name' => "Bolshevik Revolution",
        'lan_start' => "2100-10-11 12:00:00",
        'lan_end' => "2100-10-12 12:00:00",
        'seat_reservation_start' => "2100-10-04 12:00:00",
        'tournament_reservation_start' => "2100-10-07 00:00:00",
        "event_key" => "",
        "public_key" => "",
        "secret_key" => "",
        "latitude" => -67.5,
        "longitude" => 64.033333,
        "places" => 10,
        "price" => 0,
        "rules" => '☭',
        "description" => '☭'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->paramsContent['event_key'] = env('EVENT_KEY');
        $this->paramsContent['secret_key'] = env('SECRET_KEY');
        $this->paramsContent['public_key'] = env('PUBLIC_KEY');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->paramsContent['lan_id'] = $this->lan->id;
    }

    public function testUpdate(): void
    {
        $result = $this->lanService->update(
            $this->paramsContent['lan_id'],
            $this->paramsContent['name'],
            Carbon::parse($this->paramsContent['lan_start']),
            Carbon::parse($this->paramsContent['lan_end']),
            Carbon::parse($this->paramsContent['seat_reservation_start']),
            Carbon::parse($this->paramsContent['tournament_reservation_start']),
            $this->paramsContent['event_key'],
            $this->paramsContent['public_key'],
            $this->paramsContent['secret_key'],
            $this->paramsContent['latitude'],
            $this->paramsContent['longitude'],
            $this->paramsContent['places'],
            $this->paramsContent['price'],
            $this->paramsContent['rules'],
            $this->paramsContent['description']
        );

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['seat_reservation_start'], $result->seat_reservation_start);
        $this->assertEquals($this->paramsContent['tournament_reservation_start'], $result->tournament_reservation_start);
        $this->assertEquals($this->paramsContent['event_key'], $result->event_key);
        $this->assertEquals($this->paramsContent['public_key'], $result->public_key);
        $this->assertEquals($this->paramsContent['secret_key'], $result->secret_key);
        $this->assertEquals($this->paramsContent['latitude'], $result->latitude);
        $this->assertEquals($this->paramsContent['longitude'], $result->longitude);
        $this->assertEquals($this->paramsContent['places'], $result->places);
        $this->assertEquals($this->paramsContent['price'], $result->price);
        $this->assertEquals($this->paramsContent['rules'], $result->rules);
        $this->assertEquals($this->paramsContent['description'], $result->description);
    }
}

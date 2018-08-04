<?php

namespace Tests\Unit\Repository\Lan;

use DateTime;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    protected $paramsContent = [
        'name' => "Bolshevik Revolution",
        'lan_start' => "2100-10-11 12:00:00",
        'lan_end' => "2100-10-12 12:00:00",
        'seat_reservation_start' => "2100-10-04 12:00:00",
        'tournament_reservation_start' => "2100-10-07 00:00:00",
        "event_key" => "",
        "public_key" => "",
        "secret_key" => "",
        "latitude" => -67.5,
        "longitude" => 64.0333330,
        "places" => 10,
        "price" => 0,
        "rules" => '☭',
        "description" => '☭'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->paramsContent['event_key'] = env('EVENT_KEY');
        $this->paramsContent['secret_key'] = env('SECRET_KEY');
        $this->paramsContent['public_key'] = env('PUBLIC_KEY');

        $this->lan = factory('App\Model\Lan')->create();

        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testUpdate(): void
    {
        $this->lanRepository->update(
            $this->lan,
            $this->paramsContent['name'],
            new DateTime($this->paramsContent['lan_start']),
            new DateTime($this->paramsContent['lan_end']),
            new DateTime($this->paramsContent['seat_reservation_start']),
            new DateTime($this->paramsContent['tournament_reservation_start']),
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
        $this->seeInDatabase('lan', [
            'name' => $this->paramsContent['name'],
            'lan_start' => $this->paramsContent['lan_start'],
            'lan_end' => $this->paramsContent['lan_end'],
            'seat_reservation_start' => $this->paramsContent['seat_reservation_start'],
            'tournament_reservation_start' => $this->paramsContent['tournament_reservation_start'],
            'event_key' => $this->paramsContent['event_key'],
            'public_key' => $this->paramsContent['public_key'],
            'secret_key' => $this->paramsContent['secret_key'],
            'latitude' => $this->paramsContent['latitude'],
            'longitude' => $this->paramsContent['longitude'],
            'places' => $this->paramsContent['places'],
            'price' => $this->paramsContent['price'],
            'rules' => $this->paramsContent['rules'],
            'description' => $this->paramsContent['description'],
        ]);
    }
}

<?php

namespace Tests\Unit\Repository\Lan;

use DateTime;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateLanTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $paramsContent = [
        'name' => "Bolshevik Revolution",
        'lan_start' => "2100-10-11 12:00:00",
        'lan_end' => "2100-10-12 12:00:00",
        'seat_reservation_start' => "2100-10-04 12:00:00",
        'tournament_reservation_start' => "2100-10-07 00:00:00",
        "event_key_id" => "",
        "public_key_id" => "",
        "secret_key_id" => "",
        "latitude" => -67.5,
        "longitude" => 64.0333330,
        "price" => 0,
        "rules" => '☭',
        "description" => '☭'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->paramsContent['event_key_id'] = env('EVENT_KEY_ID');
        $this->paramsContent['secret_key_id'] = env('SECRET_KEY_ID');
        $this->paramsContent['public_key_id'] = env('PUBLIC_KEY_ID');

        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testCreateLan()
    {
        // Dates cause problems with Travis CI
        $this->lanRepository->createLan(
            $this->paramsContent['name'],
            new DateTime($this->paramsContent['lan_start']),
            new DateTime($this->paramsContent['lan_end']),
            new DateTime($this->paramsContent['seat_reservation_start']),
            new DateTime($this->paramsContent['tournament_reservation_start']),
            $this->paramsContent['event_key_id'],
            $this->paramsContent['public_key_id'],
            $this->paramsContent['secret_key_id'],
            $this->paramsContent['latitude'],
            $this->paramsContent['longitude'],
            $this->paramsContent['price'],
            $this->paramsContent['rules'],
            $this->paramsContent['description']
        );
        $this->seeInDatabase('lan', [
            'name' => $this->paramsContent['name'],
//            'lan_start' => $this->paramsContent['lan_start'],
//            'lan_end' => $this->paramsContent['lan_end'],
//            'seat_reservation_start' => $this->paramsContent['seat_reservation_start'],
//            'tournament_reservation_start' => $this->paramsContent['tournament_reservation_start'],
            'event_key_id' => $this->paramsContent['event_key_id'],
            'public_key_id' => $this->paramsContent['public_key_id'],
            'secret_key_id' => $this->paramsContent['secret_key_id'],
            'latitude' => $this->paramsContent['latitude'],
            'longitude' => $this->paramsContent['longitude'],
            'price' => $this->paramsContent['price'],
            'rules' => $this->paramsContent['rules'],
            'description' => $this->paramsContent['description'],
        ]);
    }
}

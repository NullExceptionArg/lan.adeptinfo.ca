<?php

namespace Tests\Unit\Repository\Lan;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetReservedPlacesTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetReservedPlacesSimple(): void
    {
        $result = $this->lanRepository->getReservedPlaces($this->lan->id);

        $this->assertEquals(0, $result);
    }

    public function testGetReservedPlacesReservation(): void
    {
        $user = factory('App\Model\User')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $user->id;
        $reservation->seat_id = env('SEAT_ID');
        $reservation->save();

        $result = $this->lanRepository->getReservedPlaces($this->lan->id);

        $this->assertEquals(1, $result);
    }
}

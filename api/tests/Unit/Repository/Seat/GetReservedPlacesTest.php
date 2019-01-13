<?php

namespace Tests\Unit\Repository\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetReservedPlacesTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetReservedPlacesSimple(): void
    {
        $result = $this->seatRepository->getReservedPlaces($this->lan->id);

        $this->assertEquals(0, $result);
    }

    public function testGetReservedPlacesReservation(): void
    {
        $user = factory('App\Model\User')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $user->id;
        $reservation->seat_id = env('SEAT_TEST_ID');
        $reservation->save();

        $result = $this->seatRepository->getReservedPlaces($this->lan->id);

        $this->assertEquals(1, $result);
    }
}

<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetCurrentSeatTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetCurrentSeat(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => env('SEAT_TEST_ID')
        ]);

        $result = $this->seatRepository->getCurrentSeat($this->user->id, $this->lan->id);

        $this->assertEquals($reservation->id, $result->id);
        $this->assertEquals($reservation->seat_id, $result->seat_id);
    }
}

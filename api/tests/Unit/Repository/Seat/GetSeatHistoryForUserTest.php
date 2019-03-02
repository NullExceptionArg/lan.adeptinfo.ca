<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class GetSeatHistoryForUserTest extends SeatsTestCase
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

    public function testGetSeatHistoryForUser(): void
    {
        $reservation = factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => env('SEAT_TEST_ID')
        ]);

        $result = $this->seatRepository->getSeatHistoryForUser($this->user->id, $this->lan->id);

        $this->assertEquals($reservation->lan_id, $result[0]->lan_id);
        $this->assertEquals($reservation->seat_id, $result[0]->seat_id);
    }
}

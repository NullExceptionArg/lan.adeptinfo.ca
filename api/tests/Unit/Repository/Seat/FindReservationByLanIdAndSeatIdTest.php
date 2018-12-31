<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class FindReservationByLanIdAndSeatIdTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testFindReservationByLanIdAndUserId(): void
    {
        factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => env('SEAT_ID')
        ]);

        $result = $this->seatRepository->findReservationByLanIdAndUserId($this->user->id, $this->lan->id);
        $this->assertEquals($this->lan->id, $result->lan_id);
        $this->assertEquals($this->user->id, $result->user_id);
    }
}

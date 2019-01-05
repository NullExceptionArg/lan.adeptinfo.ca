<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class FindReservationByLanIdAndUserIdTest extends SeatsTestCase
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

    public function testFindReservationByLanIdAndSeatId(): void
    {
        factory('App\Model\Reservation')->create([
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => env('SEAT__TEST_ID')
        ]);

        $result = $this->seatRepository->findReservationByLanIdAndSeatId($this->user->id, env('SEAT__TEST_ID'));
        $this->assertEquals($this->lan->id, $result->lan_id);
        $this->assertEquals($this->user->id, $result->user_id);
    }
}

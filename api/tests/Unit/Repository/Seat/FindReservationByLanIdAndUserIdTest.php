<?php

namespace Tests\Unit\Repository\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class FindReservationByLanIdAndUserIdTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    protected $paramsContent = [
        'seat_id' => "A-1"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');
    }

    public function testFindReservationByLanIdAndSeatId()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $reservation = new Reservation();
        $reservation->lan_id = $lan->id;
        $reservation->user_id = $user->id;
        $reservation->seat_id = $this->paramsContent['seat_id'];
        $reservation->save();

        $result = $this->seatRepository->findReservationByLanIdAndSeatId($user->id, $this->paramsContent['seat_id']);
        $this->assertEquals($lan->id, $result->lan_id);
        $this->assertEquals($user->id, $result->user_id);
    }
}

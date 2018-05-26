<?php

namespace Tests\Unit\Repository\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class FindReservationByLanIdAndUserIdTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    protected $user;
    protected $lan;

    protected $paramsContent = [
        'seat_id' => "A-1"
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testFindReservationByLanIdAndSeatId(): void
    {
        $reservation = new Reservation();
        $reservation->lan_id = $this->lan->id;
        $reservation->user_id = $this->user->id;
        $reservation->seat_id = $this->paramsContent['seat_id'];
        $reservation->save();

        $result = $this->seatRepository->findReservationByLanIdAndSeatId($this->user->id, $this->paramsContent['seat_id']);
        $this->assertEquals($this->lan->id, $result->lan_id);
        $this->assertEquals($this->user->id, $result->user_id);
    }
}

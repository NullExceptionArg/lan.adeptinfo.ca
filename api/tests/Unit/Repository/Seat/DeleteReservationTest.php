<?php

namespace Tests\Unit\Repository\Seat;

use App\Model\Reservation;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class DeleteReservationTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatRepository;

    protected $user;
    protected $lan;
    protected $reservation;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->reservation = factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
    }

    public function testFindReservationByLanIdAndUserId(): void
    {
        $this->seeInDatabase('reservation', [
            'lan_id' => $this->reservation->lan_id,
            'user_id' => $this->reservation->user_id,
            'seat_id' => $this->reservation->seat_id
        ]);

        $this->seatRepository->deleteReservation($this->reservation->id);

        $reservation = Reservation::onlyTrashed()->first();
        $this->assertEquals($this->reservation->lan_id, $reservation->lan_id);
        $this->assertEquals($this->reservation->user_id, $reservation->user_id);
        $this->assertEquals($this->reservation->seat_id, $reservation->seat_id);
    }
}

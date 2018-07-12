<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class SetReservationArrivedTest extends SeatsTestCase
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
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id
        ]);
    }

    public function testSetReservationArrived(): void
    {
        $this->seeInDatabase('reservation', [
            'lan_id' => $this->reservation->lan_id,
            'user_id' => $this->reservation->user_id,
            'seat_id' => $this->reservation->seat_id,
            'arrived_at' => null
        ]);
        $this->seatRepository->setReservationArrived($this->reservation);
        $this->seeInDatabase('reservation', [
            'lan_id' => $this->reservation->lan_id,
            'user_id' => $this->reservation->user_id,
            'seat_id' => $this->reservation->seat_id,
            'arrived_at' => $this->reservation->arrived_at
        ]);
    }
}

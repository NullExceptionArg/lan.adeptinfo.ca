<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class SetReservationLeftTest extends TestCase
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

    public function testSetReservationLeft(): void
    {
        $this->seeInDatabase('reservation', [
            'lan_id' => $this->reservation->lan_id,
            'user_id' => $this->reservation->user_id,
            'seat_id' => $this->reservation->seat_id,
            'left_at' => null
        ]);

        $this->seatRepository->setReservationLeft($this->reservation, $this->lan->id);

        $this->seeInDatabase('reservation', [
            'lan_id' => $this->reservation->lan_id,
            'user_id' => $this->reservation->user_id,
            'seat_id' => $this->reservation->seat_id,
            'left_at' => $this->reservation->left_at
        ]);
    }
}

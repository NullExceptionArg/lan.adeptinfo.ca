<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class CreateReservationTest extends SeatsTestCase
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

    public function testCreateReservation(): void
    {
        $this->seatRepository->createReservation($this->user->id, $this->lan->id, env('SEAT_TEST_ID'));
        $this->seeInDatabase('reservation', [
            'lan_id'  => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => env('SEAT_TEST_ID'),
        ]);
    }
}

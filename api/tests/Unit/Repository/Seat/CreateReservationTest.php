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

    protected $paramsContent = [
        'seat_id' => "A-1"
    ];

    public function setUp()
    {
        parent::setUp();
        $this->seatRepository = $this->app->make('App\Repositories\Implementation\SeatRepositoryImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testAttachUser()
    {
        $this->seatRepository->createReservation($this->user, $this->lan, $this->paramsContent['seat_id']);
        $this->seeInDatabase('reservation', [
            'lan_id' => $this->lan->id,
            'user_id' => $this->user->id,
            'seat_id' => $this->paramsContent['seat_id']
        ]);
    }
}

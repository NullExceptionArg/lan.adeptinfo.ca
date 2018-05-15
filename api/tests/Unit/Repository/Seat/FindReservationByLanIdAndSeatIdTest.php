<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class FindReservationByLanIdAndSeatIdTest extends SeatsTestCase
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

    public function testFindReservationByLanIdAndUserId()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $this->seatRepository->attachLanUser($user, $lan, $this->paramsContent['seat_id']);

        $result = $this->seatRepository->findReservationByLanIdAndUserId($user->id, $lan->id);
        $this->assertEquals($lan->id, $result->lan_id);
        $this->assertEquals($user->id, $result->user_id);
    }
}

<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class UnBookTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $seatService;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->be($this->user);
    }

    public function testUnBookSeat(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $result = $this->seatService->unBook($this->lan->id, env('SEAT_ID'));

        $this->assertEquals(env('SEAT_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }
}

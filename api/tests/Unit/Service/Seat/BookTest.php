<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class BookTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->be($this->user);
    }

    public function testBook(): void
    {
        $result = $this->seatService->book($this->lan->id, env('SEAT_TEST_ID'));

        $this->assertEquals(env('SEAT_TEST_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }
}

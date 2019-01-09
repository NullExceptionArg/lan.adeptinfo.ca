<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class AssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $admin;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testAssignSeat(): void
    {
        $result = $this->seatService->assign($this->lan->id, $this->user->email, env('SEAT_TEST_ID'));

        $this->assertEquals(env('SEAT_TEST_ID'), $result->seat_id);
        $this->assertEquals($this->lan->id, $result->lan_id);
    }
}

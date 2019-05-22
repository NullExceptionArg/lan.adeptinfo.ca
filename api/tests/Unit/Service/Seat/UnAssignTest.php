<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class UnAssignTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $admin;
    protected $lan;
    protected $seatService;

    protected $requestContent = [
        'lan_id'  => null,
        'seat_id' => null,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testUnAssign(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $result = $this->seatService->unAssign($this->lan->id, $this->user->email, env('SEAT_TEST_ID'));

        $this->assertEquals(env('SEAT_TEST_ID'), $result);
    }
}

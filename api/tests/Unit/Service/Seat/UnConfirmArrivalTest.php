<?php

namespace Tests\Unit\Service\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class UnConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $seatService;

    protected $user;
    protected $lan;
    protected $reservation;

    public function setUp(): void
    {
        parent::setUp();
        $this->seatService = $this->app->make('App\Services\Implementation\SeatServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testUnConfirmArrival(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id'  => $this->lan->id,
        ]);
        $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT_TEST_ID')], 'arrived');

        $result = $this->seatService->unConfirmArrival($this->lan->id, env('SEAT_TEST_ID'));

        $this->assertEquals(env('SEAT_TEST_ID'), $result);
    }
}

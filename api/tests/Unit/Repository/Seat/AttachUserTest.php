<?php

namespace Tests\Unit\Repository\Seat;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class AttachUserTest extends SeatsTestCase
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

    public function testAttachUser()
    {
        $user = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();

        $this->seatRepository->attachLanUser($user, $lan, $this->paramsContent['seat_id']);
        $this->seeInDatabase('reservation', [
            'lan_id' => $lan->id,
            'user_id' => $user->id,
            'seat_id' => $this->paramsContent['seat_id']
        ]);
    }
}

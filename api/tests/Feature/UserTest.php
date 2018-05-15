<?php

namespace Tests\Feature;

use App\Model\Reservation;
use App\Model\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class UserTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $lanService;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    // Should be updated every time the user has a new relation
    public function testDeleteUserAndRelations()
    {

        $user = factory('App\Model\User')->create();
        $this->be($user);
        $lan = factory('App\Model\Lan')->create();
        $seatsClient = new SeatsioClient($lan->secret_key_id);

        ///Building relations
        // Lan relation
        $this->call('POST', '/api/lan/' . $lan->id . '/book/' . 'A-1');

        /// Make sure every relations exist
        // Reservation
        $this->assertEquals(1, Reservation::where('user_id', $user->id)->get()->count());

        // Seats.io
        $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, 'A-1');
        $this->assertEquals('booked', $status->status);

        /// Delete user
        $this->actingAs($user)
            ->json('DELETE', '/api/user')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);

        /// Verifying every relations have been removed
        // User
        $this->assertEquals(0, User::where('id', $user->id)->get()->count());

        // Reservation
        $this->assertEquals(0, Reservation::where('user_id', $user->id)->get()->count());

        // Seats.io
        $status = $seatsClient->events()->retrieveObjectStatus($lan->event_key_id, 'A-1');
        $this->assertEquals('free', $status->status);

    }
}

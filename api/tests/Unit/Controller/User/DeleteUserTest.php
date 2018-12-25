<?php

namespace Tests\Unit\Controller\User;

use App\Model\Contribution;
use App\Model\Reservation;
use App\Model\User;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    public function testDeleteUserSimple(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);

        $this->json('DELETE', '/api/user')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);
    }

    // Should be updated every time the user has a new relation
    public function testDeleteUserComplex(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $lan = factory('App\Model\Lan')->create();
        $seatsClient = new SeatsioClient($lan->secret_key);
        $contributionCategory = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $user->id
        ]);

        ///Building relations
        // Lan - Reservation
        $this->call('POST', '/api/seat/book/' . 'A-1', [
            'lan_id' => $lan->id
        ]);

        // Contribution - Contribution Category
        $contribution->ContributionCategory()->attach($contributionCategory);

        /// Make sure every relations exist
        // Reservation - GetUserResource
        $this->assertEquals(1, Reservation::where('user_id', $user->id)->get()->count());

        // Contribution - GetUserResource
        $this->assertEquals(1, $user->Contribution()->count());

        // Seats.io
        $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, 'A-1');
        $this->assertEquals('booked', $status->status);

        /// Delete user
        $this->actingAs($user)
            ->json('DELETE', '/api/user')
            ->seeJsonEquals([])
            ->assertResponseStatus(200);

        /// Verify relations have been removed
        // GetUserResource
        $this->assertEquals(0, User::where('id', $user->id)->get()->count());

        // Reservation
        $this->assertEquals(0, Reservation::where('user_id', $user->id)->get()->count());

        // Contribution
        $this->assertEquals(0, Contribution::where('user_id', $user->id)->count());

        // Seats.io
        $status = $seatsClient->events->retrieveObjectStatus($lan->event_key, 'A-1');
        $this->assertEquals('free', $status->status);
    }
}

<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Seatsio\SeatsioClient;
use Tests\SeatsTestCase;

class ConfirmArrivalTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $reservation;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'confirm-arrival'
        );
    }

    public function testConfirmArrival(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                "seat_id" => env('SEAT_TEST_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testConfirmArrivalHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testConfirmArrivalCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan->id
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $lan->id,
            'confirm-arrival'
        );

        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'))
            ->seeJsonEquals([
                "seat_id" => env('SEAT_TEST_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testConfirmArrivalSeatLanRelationExists(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);

        $permission = Permission::where('name', 'confirm-arrival')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'))
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The relation between seat with id ' . env('SEAT_TEST_ID') . ' and LAN with id ' . $lan->id . ' doesn\'t exist.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testConfirmArrivalLanIdExist(): void
    {
        $badLanId = -1;
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $badLanId
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testConfirmArrivalLanIdInteger(): void
    {
        $badLanId = '☭';
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $badLanId
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testConfirmArrivalSeatIdExist(): void
    {
        $badSeatId = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . $badSeatId, [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.',
                        1 => 'The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testConfirmArrivalSeatIdFree(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT_TEST_ID')], 'free');

        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'This seat is not associated with a reservation.',
                        1 => 'The relation between seat with id ' . env('SEAT_TEST_ID') . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdArrived(): void
    {
        $seatsClient = new SeatsioClient($this->lan->secret_key);
        $seatsClient->events->changeObjectStatus($this->lan->event_key, [env('SEAT_TEST_ID')], 'arrived');

        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . env('SEAT_TEST_ID'), [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "This seat is already set to arrived.",
                        1 => 'The relation between seat with id ' . env('SEAT_TEST_ID') . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testBookSeatIdUnknown(): void
    {
        $badSeatId = "B4D-1D";
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/seat/confirm/' . $badSeatId, [
                'lan_id' => $this->lan->id
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => "The selected seat id is invalid.",
                        1 => 'The relation between seat with id ' . $badSeatId . ' and LAN with id ' . $this->lan->id . ' doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

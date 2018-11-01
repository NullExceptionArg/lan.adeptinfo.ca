<?php

namespace Tests\Unit\Controller\Seat;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\SeatsTestCase;

class UnAssignSeatTest extends SeatsTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $admin;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
        'seat_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->admin = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'unassign-seat')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->admin->id
        ]);
    }

    public function testUnAssignSeat(): void
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                "lan_id" => $this->lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnAssignSeatCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'unassign-seat')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->admin->id
        ]);
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $lan->id
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                "lan_id" => $lan->id,
                "seat_id" => env('SEAT_ID')
            ])
            ->assertResponseStatus(200);
    }

    public function testUnAssignSeatHasPermission(): void
    {
        $admin = factory('App\Model\User')->create();
        $this->actingAs($admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testUnAssignSeatLanIdExist()
    {
        $badLanId = -1;
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $badLanId,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignSeatIdExist()
    {
        $badSeatId = '☭';
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/'  . $badSeatId, [
                'lan_id' => $this->lan->id,
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_id' => [
                        0 => 'The selected seat id is invalid.',
                        1 => 'The relation between seat with id ' . $badSeatId . ' and LAN with id 1 doesn\'t exist.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignSeatLanIdInteger()
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => '☭',
                'user_email' => $this->user->email
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUnAssignSeatEmailExists()
    {
        factory('App\Model\Reservation')->create([
            'user_id' => $this->user->id,
            'lan_id' => $this->lan->id
        ]);
        $this->actingAs($this->admin)
            ->json('DELETE', '/api/seat/assign/' . env('SEAT_ID'), [
                'lan_id' => $this->lan->id,
                'user_email' => '☭'
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'The selected user email is invalid.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

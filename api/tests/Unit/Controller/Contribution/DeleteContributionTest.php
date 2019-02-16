<?php

namespace Tests\Unit\Controller\Contribution;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-contribution'
        );
    }

    public function testDeleteContributionUserEmail(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => $this->lan->id,
                'contribution_id' => $contribution->id
            ])
            ->seeJsonEquals([
                'id' => $contribution->id,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionUserFullName(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => $this->lan->id,
                'contribution_id' => $contribution->id
            ])
            ->seeJsonEquals([
                'id' => $contribution->id,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionCurrentLan(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'delete-contribution')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'contribution_id' => $contribution->id
            ])
            ->seeJsonEquals([
                'id' => $contribution->id,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $user->id
        ]);
        $this->actingAs($user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => $this->lan->id,
                'contribution_id' => $contribution
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testDeleteContributionLanIdExist(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => -1,
                'contribution_id' => $contribution->id
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

    public function testDeleteContributionLanIdInteger(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => '☭',
                'contribution_id' => $contribution->id
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

    public function testDeleteContributionCategoryIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => $this->lan->id,
                'contribution_id' => '☭'
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_id' => [
                        0 => 'The contribution id must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteContributionCategoryIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution', [
                'lan_id' => $this->lan->id,
                'contribution_id' => -1
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_id' => [
                        0 => 'The selected contribution id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

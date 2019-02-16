<?php

namespace Tests\Unit\Controller\Contribution;

use App\Model\{Permission};
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'delete-contribution-category'
        );
    }

    public function testDeleteCategorySimple(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteCategoryCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'delete-contribution-category')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution/category', [
                'contribution_category_id' => $category->id
            ])
            ->seeJsonEquals([
                'id' => $category->id,
                'name' => $category->name
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteCategoryPermission(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteCategoryTestCategoryIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => -1
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'The selected contribution category id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testDeleteCategoryTestCategoryIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', 'http://' . env('API_DOMAIN') . '/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => '☭'
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'The contribution category id must be an integer.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

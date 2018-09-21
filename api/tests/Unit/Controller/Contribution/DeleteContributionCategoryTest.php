<?php

namespace Tests\Unit\Controller\Contribution;


use App\Model\Contribution;
use App\Model\ContributionCategory;
use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionCategoryTest extends TestCase
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

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
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
    }

    public function testDeleteContributionCategorySimple(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionCategoryCurrentLan(): void
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
            ->json('DELETE', '/api/contribution/category', [
                'contribution_category_id' => $category->id
            ])
            ->seeJsonEquals([
                'id' => $category->id,
                'name' => $category->name
            ])
            ->assertResponseStatus(200);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexOnCategoryForContribution(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(1, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(0, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(0, Contribution::all()->count());
    }

    public function testDeleteContributionCategoryPermission(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);
    }

    // Should be updated every time Contribution Category has a new relation
    public function testDeleteContributionCategoryComplexManyCategoryForContribution(): void
    {
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);

        ///Building relations
        // Contribution - Contribution category relation
        $contribution->ContributionCategory()->attach($this->category);
        $contribution->ContributionCategory()->attach($category2);

        /// Make sure every relations exist
        // Lan - Contribution
        $this->assertEquals(1, $this->user->Contribution()->count());

        // Contribution - Contribution category
        $this->assertEquals(2, $contribution->ContributionCategory()->count());

        //Contribution category - Lan
        $this->assertEquals(1, $this->category->Lan()->count());

        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => $this->lan->id,
                'contribution_category_id' => $this->category->id
            ])
            ->seeJsonEquals([
                'id' => $this->category->id,
                'name' => $this->category->name
            ])
            ->assertResponseStatus(200);

        /// Verify relations have been removed
        // Contribution category
        $this->assertEquals(1, ContributionCategory::all()->count());

        // Contribution
        $this->assertEquals(1, Contribution::all()->count());
    }

    public function testDeleteContributionCategoryTestLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => -1,
                'contribution_category_id' => $this->category->id
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

    public function testDeleteContributionCategoryTestLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
                'lan_id' => '☭',
                'contribution_category_id' => $this->category->id
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

    public function testDeleteContributionCategoryTestCategoryIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
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

    public function testDeleteContributionCategoryTestCategoryIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution/category', [
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

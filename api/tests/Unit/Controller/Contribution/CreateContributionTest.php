<?php

namespace Tests\Unit\Controller\Contribution;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;
    protected $category;

    protected $requestContent = [
        'contribution_category_id' => null,
        'user_full_name' => null,
        'user_email' => null,
        'lan_id' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->requestContent['contribution_category_id'] = $this->category->id;
        $this->requestContent['lan_id'] = $this->lan->id;

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'create-contribution'
        );
    }

    public function testCreateContributionUserFullName(): void
    {
        $this->requestContent['user_full_name'] = $this->user->getFullName();
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionNoLan(): void
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
        $permission = Permission::where('name', 'create-contribution')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['user_full_name'] = $this->user->getFullName();
        $this->requestContent['lan_id'] = $lan->id;
        $this->requestContent['contribution_category_id'] = $category->id;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testCreateContributionUserEmail(): void
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionCategoryIdRequired(): void
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'The contribution category id field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionCategoryIdInteger(): void
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'contribution_category_id' => [
                        0 => 'The contribution category id must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionCategoryIdExist(): void
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
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

    public function testCreateContributionUserFullNameString(): void
    {
        $this->requestContent['user_full_name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_full_name' => [
                        0 => 'The user full name must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionUserEmailString(): void
    {
        $this->requestContent['user_email'] = 1;
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'The user email must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionUserFullNameOrUserEmailNotNull(): void
    {
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'The user email field is required when user full name is not present.',
                    ],
                    'user_full_name' => [
                        0 => 'The user full name field is required when user email is not present.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateContributionUserEmailAndUserFullNameNotFilled(): void
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['user_full_name'] = $this->user->getFullName();
        $this->actingAs($this->user)
            ->json('POST', 'http://' . env('API_DOMAIN') . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'Field user email can\'t be used if the field user_full_name is used too.',
                    ],
                    'user_full_name' => [
                        0 => 'Field user full name can\'t be used if the field user_email is used too.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

}

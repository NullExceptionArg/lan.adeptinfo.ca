<?php

namespace Tests\Unit\Controller\Contribution;

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
    ];

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->requestContent['contribution_category_id'] = $this->category->id;
    }

    public function testCreateContributionUserFullName()
    {
        $this->requestContent['user_full_name'] = $this->user->getFullName();
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'user_full_name' => $this->user->getFullName(),
                'contribution_category_id' => $this->category->id
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionUserEmail()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'user_full_name' => $this->user->getFullName(),
                'contribution_category_id' => $this->category->id
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateContributionLanIdExist()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/contribution', $this->requestContent)
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

    public function testCreateContributionLanIdInteger()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/contribution', $this->requestContent)
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

    public function testCreateContributionCategoryIdRequired()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = null;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionCategoryIdInteger()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionCategoryIdExist()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['contribution_category_id'] = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionUserFullNameString()
    {
        $this->requestContent['user_full_name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionUserEmailString()
    {
        $this->requestContent['user_email'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionUserFullNameOrUserEmailNotNull()
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
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

    public function testCreateContributionUserEmailAndUserFullNameNotFilled()
    {
        $this->requestContent['user_email'] = $this->user->email;
        $this->requestContent['user_full_name'] = $this->user->getFullName();
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/contribution', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'user_email' => [
                        0 => 'Field can\'t be used if user_full_name is used too.',
                    ],
                    'user_full_name' => [
                        0 => 'Field can\'t be used if user_email is used too.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

}

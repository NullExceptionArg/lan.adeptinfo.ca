<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetContributionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetContributions(): void
    {
        $category1 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id,
        ]);
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id,
        ]);

        $contributions1 = factory('App\Model\Contribution', 3)->create([
            'user_id'                  => $this->user->id,
            'contribution_category_id' => $category1->id,
        ]);
        $contributions2 = factory('App\Model\Contribution', 3)->create([
            'user_full_name'           => $this->user->getFullName(),
            'contribution_category_id' => $category2->id,
        ]);

        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/contribution', [
                'lan_id' => $this->lan->id,
            ])
            ->seeJsonEquals([
                [
                    'category_id'   => $category1->id,
                    'category_name' => $category1->name,
                    'contributions' => [
                        [
                            'id'             => $contributions1[0]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions1[1]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions1[2]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                    ],
                ],
                [
                    'category_id'   => $category2->id,
                    'category_name' => $category2->name,
                    'contributions' => [
                        [
                            'id'             => $contributions2[0]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions2[1]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions2[2]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetContributionsCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $category1 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id,
        ]);
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id,
        ]);

        $contributions1 = factory('App\Model\Contribution', 3)->create([
            'user_id'                  => $this->user->id,
            'contribution_category_id' => $category1->id,
        ]);
        $contributions2 = factory('App\Model\Contribution', 3)->create([
            'user_full_name'           => $this->user->getFullName(),
            'contribution_category_id' => $category2->id,
        ]);

        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/contribution')
            ->seeJsonEquals([
                [
                    'category_id'   => $category1->id,
                    'category_name' => $category1->name,
                    'contributions' => [
                        [
                            'id'             => $contributions1[0]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions1[1]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions1[2]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                    ],
                ],
                [
                    'category_id'   => $category2->id,
                    'category_name' => $category2->name,
                    'contributions' => [
                        [
                            'id'             => $contributions2[0]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions2[1]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                        [
                            'id'             => $contributions2[2]->id,
                            'user_full_name' => $this->user->getFullName(),
                        ],
                    ],
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetContributionsLanIdExist(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/contribution', [
                'lan_id' => -1,
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetContributionsLanIdInteger(): void
    {
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/contribution', [
                'lan_id' => '☭',
            ])
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The lan id must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}

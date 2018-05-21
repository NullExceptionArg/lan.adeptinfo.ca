<?php

namespace Tests\Unit\Controller\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetContributionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetContributions()
    {
        $category1 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $category2 = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);

        $contributions1 = factory('App\Model\Contribution', 10)->create([
            'user_id' => $this->user->id
        ]);
        $contributions2 = factory('App\Model\Contribution', 10)->create([
            'user_full_name' => $this->user->getFullName()
        ]);

        $expectedContributions1 = null;
        for ($i = 0; $i < 10; $i++) {
            $category1->Contribution()->attach($contributions1[$i]);
            $expectedContributions1[$i] = [
                "id" => $contributions1[$i]->id,
                "user_full_name" => $this->user->getFullName(),
            ];
        }

        $expectedContributions2 = null;
        for ($i = 0; $i < 10; $i++) {
            $category2->Contribution()->attach($contributions2[$i]);
            $expectedContributions2[$i] = [
                "id" => $contributions2[$i]->id,
                "user_full_name" => $this->user->getFullName(),
            ];
        }

        $this->actingAs($this->user)
            ->json('GET', '/api/lan/' . $this->lan->id . '/contribution')
            ->seeJsonEquals([
                [
                    'category_id' => $category1->id,
                    'category_name' => $category1->name,
                    'contributions' => $expectedContributions1
                ],
                [
                    'category_id' => $category2->id,
                    'category_name' => $category2->name,
                    'contributions' => $expectedContributions2
                ]
            ])
            ->assertResponseStatus(200);
    }

    public function testGetContributionsLanIdExist()
    {
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('GET', '/api/lan/' . $badLanId . '/contribution')
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

    public function testGetContributionsLanIdInteger()
    {
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('GET', '/api/lan/' . $badLanId . '/contribution')
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
}

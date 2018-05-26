<?php

namespace Tests\Unit\Controller\Contribution;

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
    }

    public function testDeleteContributionUserEmail(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution/' . $contribution->id)
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
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution/' . $contribution->id)
            ->seeJsonEquals([
                'id' => $contribution->id,
                'user_full_name' => $this->user->getFullName()
            ])
            ->assertResponseStatus(200);
    }

    public function testDeleteContributionLanIdExist(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);
        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution/' . $contribution->id)
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
        $badLanId = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $badLanId . '/contribution/' . $contribution->id)
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
        $badContributionId = '☭';
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution/' . $badContributionId)
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
        $badContributionId = -1;
        $this->actingAs($this->user)
            ->json('DELETE', '/api/lan/' . $this->lan->id . '/contribution/' . $badContributionId)
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

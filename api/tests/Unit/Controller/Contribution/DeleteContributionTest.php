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
            ->json('DELETE', '/api/contribution', [
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
            ->json('DELETE', '/api/contribution', [
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
        factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution', [
                'contribution_id' => $contribution->id
            ])
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
        $this->actingAs($this->user)
            ->json('DELETE', '/api/contribution', [
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
            ->json('DELETE', '/api/contribution', [
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
            ->json('DELETE', '/api/contribution', [
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
            ->json('DELETE', '/api/contribution', [
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

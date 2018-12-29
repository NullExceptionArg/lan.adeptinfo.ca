<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $user;
    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'create-contribution'
        );

        $this->be($this->user);
    }

    public function testCreateContributionUserFullName(): void
    {
        $result = $this->contributorService->createContribution($this->category->id, $this->user->getFullName(), null);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testCreateContributionUserEmail(): void
    {
        $result = $this->contributorService->createContribution($this->category->id, null, $this->user->email);
        $result = $result->jsonSerialize();

        $this->assertEquals(1, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }
}

<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $lan;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->user = factory('App\Model\User')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'create-contribution-category'
        );
    }

    public function testCreateCategory(): void
    {
        $name = 'Programmer';
        $result = $this->contributorService->createCategory($this->lan->id, $name);
        $this->assertEquals($name, $result['name']);
    }
}

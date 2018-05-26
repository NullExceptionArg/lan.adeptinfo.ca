<?php

namespace Tests\Unit\Repository\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteCategoryByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionRepository;

    protected $lan;
    protected $category;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionRepository = $this->app->make('App\Repositories\Implementation\ContributionRepositoryImpl');
        $this->lan = factory('App\Model\Lan')->create();
        $this->category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteCategory(): void
    {
        $this->seeInDatabase('contribution_category', [
            'id' => $this->category->id,
            'name' => $this->category->name
        ]);

        $this->contributionRepository->deleteCategoryById($this->category->id);

        $this->notSeeInDatabase('contribution_category', [
            'id' => $this->category->id,
            'name' => $this->category->name
        ]);
    }
}

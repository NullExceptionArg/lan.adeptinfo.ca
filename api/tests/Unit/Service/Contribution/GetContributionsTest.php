<?php

namespace Tests\Unit\Service\Contribution;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetContributionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributionService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributionService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testGetContributions(): void
    {
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $this->lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);

        $category->Contribution()->attach($contribution);

        $result = $this->contributionService->getContributions($this->lan->id);
        $this->assertEquals($category->id, $result[0]->id);
        $this->assertEquals($category->name, $result[0]->name);
        $this->assertEquals([
            'id' => $contribution->id,
            'user_full_name' => $this->user->getFullName()
        ], $result[0]->contribution[0]->toArray());
    }

    public function testGetContributionsLanIdExist(): void
    {
        $badLanId = -1;
        try {
            $this->contributionService->getContributions($badLanId);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetContributionsLanIdInteger(): void
    {
        $badLanId = '☭';
        try {
            $this->contributionService->getContributions($badLanId);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

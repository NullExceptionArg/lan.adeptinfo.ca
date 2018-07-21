<?php

namespace Tests\Unit\Service\Contribution;

use Illuminate\Http\Request;
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

        $request = new Request([
            'lan_id' => $this->lan->id
        ]);

        $result = $this->contributionService->getContributions($request);
        $this->assertEquals($category->id, $result[0]->id);
        $this->assertEquals($category->name, $result[0]->name);
        $this->assertEquals([
            'id' => $contribution->id,
            'user_full_name' => $this->user->getFullName()
        ], $result[0]->contribution[0]->toArray());
    }

    public function testGetContributionsCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $category = factory('App\Model\ContributionCategory')->create([
            'lan_id' => $lan->id
        ]);
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);

        $category->Contribution()->attach($contribution);

        $request = new Request();

        $result = $this->contributionService->getContributions($request);
        $this->assertEquals($category->id, $result[0]->id);
        $this->assertEquals($category->name, $result[0]->name);
        $this->assertEquals([
            'id' => $contribution->id,
            'user_full_name' => $this->user->getFullName()
        ], $result[0]->contribution[0]->toArray());
    }

    public function testGetContributionsLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->contributionService->getContributions($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetContributionsLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->contributionService->getContributions($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

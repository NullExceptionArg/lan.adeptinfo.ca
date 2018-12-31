<?php

namespace Tests\Unit\Service\Contribution;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteContributionTest extends TestCase
{
    use DatabaseMigrations;

    protected $contributorService;

    protected $user;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->contributorService = $this->app->make('App\Services\Implementation\ContributionServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
    }

    public function testDeleteContributionUserEmail(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_id' => $this->user->id
        ]);

        $result = $this->contributorService->deleteContribution($contribution->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionUserFullName(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);

        $result = $this->contributorService->deleteContribution($contribution->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }

    public function testDeleteContributionCurrentLan(): void
    {
        $contribution = factory('App\Model\Contribution')->create([
            'user_full_name' => $this->user->getFullName()
        ]);
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'delete-contribution')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $result = $this->contributorService->deleteContribution($contribution->id);
        $result = $result->jsonSerialize();

        $this->assertEquals($contribution->id, $result['id']);
        $this->assertEquals($this->user->getFullName(), $result['user_full_name']);
    }
}

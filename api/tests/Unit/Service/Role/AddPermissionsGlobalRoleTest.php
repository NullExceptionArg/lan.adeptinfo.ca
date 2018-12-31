<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class AddPermissionsGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;
    protected $permissions;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();
        $this->permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();
    }

    public function testAddPermissionsGlobalRole(): void
    {
        $result = $this->roleService->addPermissionsGlobalRole($this->globalRole->id, $this->permissions);

        $this->assertEquals($this->globalRole->name, $result->name);
        $this->assertEquals($this->globalRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->globalRole->en_description, $result->en_description);
        $this->assertEquals($this->globalRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->globalRole->fr_description, $result->fr_description);
    }
}

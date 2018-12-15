<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetGlobalRolePermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $globalRole;
    protected $permissions;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->globalRole = factory('App\Model\GlobalRole')->create();
        $this->permissions = Permission::inRandomOrder()
            ->take(3)
            ->get();

        foreach ($this->permissions as $permission) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permission->id
            ]);
        }
    }

    public function testGetGlobalRolePermissions(): void
    {
        $result = $this->roleRepository->getGlobalRolePermissions($this->globalRole->id);

        $this->assertEquals($this->permissions[0]['id'], $result[0]->id);
        $this->assertEquals($this->permissions[0]['name'], $result[0]->name);

        $this->assertEquals($this->permissions[1]['id'], $result[1]->id);
        $this->assertEquals($this->permissions[1]['name'], $result[1]->name);

        $this->assertEquals($this->permissions[2]['id'], $result[2]->id);
        $this->assertEquals($this->permissions[2]['name'], $result[2]->name);
    }
}

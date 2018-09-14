<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAdminPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
    }

    public function testGetAdminPermissions(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->take(5)
            ->get();
        $role = factory('App\Model\Role')->create([
            'lan_id' => $lan->id
        ]);
        foreach ($permissions as $permission) {
            factory('App\Model\PermissionRole')->create([
                'permission_id' => $permission->id,
                'role_id' => $role->id
            ]);
        }
        factory('App\Model\RoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $role->id
        ]);
        $permissionsResult = $this->roleRepository->getAdminPermissions($lan, $this->user);

        $this->assertEquals($permissions[0]->id, $permissionsResult[0]->id);
        $this->assertEquals($permissions[0]->name, $permissionsResult[0]->name);

        $this->assertEquals($permissions[1]->id, $permissionsResult[1]->id);
        $this->assertEquals($permissions[1]->name, $permissionsResult[1]->name);

        $this->assertEquals($permissions[2]->id, $permissionsResult[2]->id);
        $this->assertEquals($permissions[2]->name, $permissionsResult[2]->name);

        $this->assertEquals($permissions[3]->id, $permissionsResult[3]->id);
        $this->assertEquals($permissions[3]->name, $permissionsResult[3]->name);

        $this->assertEquals($permissions[4]->id, $permissionsResult[4]->id);
        $this->assertEquals($permissions[4]->name, $permissionsResult[4]->name);
    }
}

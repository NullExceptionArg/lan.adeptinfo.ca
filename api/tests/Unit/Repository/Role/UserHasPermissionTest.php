<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserHasPermissionTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $role;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testUserHasPermissionTrue(): void
    {
        $permission = Permission::where('can_be_per_lan', true)
            ->first();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $result = $this->roleRepository->userHasPermission(
            $permission->name,
            $this->user->id,
            $this->lan->id
        );

        $this->assertEquals($result, true);
    }

    public function testUserHasPermissionFalse(): void
    {
        $permission = Permission::where('can_be_per_lan', true)
            ->first();

        $result = $this->roleRepository->userHasPermission(
            $permission->name,
            $this->user->id,
            $this->lan->id
        );

        $this->assertEquals($result, false);
    }
}

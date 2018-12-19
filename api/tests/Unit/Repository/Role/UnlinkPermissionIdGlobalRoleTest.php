<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UnlinkPermissionIdGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->role = factory('App\Model\GlobalRole')->create();
        $this->permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $this->role->id,
            'permission_id' => $this->permissions[0]
        ]);
    }

    public function testCreateGlobalRole(): void
    {
        $this->seeInDatabase('permission_global_role', [
            'permission_id' => $this->permissions[0],
            'role_id' => $this->role->id
        ]);

        $this->roleRepository->unlinkPermissionIdGlobalRole(
            $this->permissions[0],
            $this->role
        );

        $this->notSeeInDatabase('permission_global_role', [
            'permission_id' => $this->permissions[0],
            'role_id' => $this->role->id
        ]);
    }
}

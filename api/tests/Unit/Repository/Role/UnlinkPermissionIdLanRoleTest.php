<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UnlinkPermissionIdLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $role;
    protected $lan;
    protected $permissions;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(10)
            ->pluck('id')
            ->toArray();

        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $this->role->id,
            'permission_id' => $this->permissions[0]
        ]);
    }

    public function testUnlinkPermissionIdLanRole(): void
    {
        $this->seeInDatabase('permission_lan_role', [
            'permission_id' => $this->permissions[0],
            'role_id' => $this->role->id
        ]);

        $this->roleRepository->unlinkPermissionIdLanRole(
            $this->permissions[0],
            $this->role->id
        );

        $this->notSeeInDatabase('permission_lan_role', [
            'permission_id' => $this->permissions[0],
            'role_id' => $this->role->id
        ]);
    }
}

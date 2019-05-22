<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanRoleTest extends TestCase
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
            'lan_id' => $this->lan->id,
        ]);

        $permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id'       => $this->role->id,
                'permission_id' => $permissionId,
            ]);
        }

        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->role->id,
        ]);
    }

    public function testDeleteLanRole(): void
    {
        $this->seeInDatabase('lan_role', [
            'id'   => $this->role->id,
            'name' => $this->role->name,
        ]);

        $this->roleRepository->deleteLanRole(
            $this->role->id
        );

        $this->notSeeInDatabase('lan_role', [
            'id'   => $this->role->id,
            'name' => $this->role->name,
        ]);
    }
}

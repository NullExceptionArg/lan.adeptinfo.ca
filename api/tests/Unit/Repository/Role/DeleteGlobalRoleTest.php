<?php

namespace Tests\Unit\Repository\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteGlobalRoleTest extends TestCase
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

        $permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->role->id,
                'permission_id' => $permissionId
            ]);
        }

        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->role->id
        ]);
    }

    public function testDeleteGlobalRole(): void
    {
        $this->seeInDatabase('global_role', [
            'id' => $this->role->id,
            'name' => $this->role->name
        ]);

        $this->roleRepository->deleteGlobalRole(
            $this->role->id
        );

        $this->notSeeInDatabase('global_role', [
            'id' => $this->role->id,
            'name' => $this->role->name
        ]);
    }
}

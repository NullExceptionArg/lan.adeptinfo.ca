<?php

namespace Tests\Unit\Repository\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinkPermissionIdGlobalRoleTest extends TestCase
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
    }

    public function testCreateGlobalRole(): void
    {
        $permission = DB::table('permission')
            ->first();

        $this->notSeeInDatabase('permission_global_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);

        $this->roleRepository->linkPermissionIdGlobalRole(
            $permission->id,
            $this->role
        );

        $this->seeInDatabase('permission_global_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);
    }
}

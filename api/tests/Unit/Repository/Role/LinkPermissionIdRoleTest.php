<?php

namespace Tests\Unit\Repository\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinkPermissionIdRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lan;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->role = factory('App\Model\Role')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testCreateTest(): void
    {
        $permission = DB::table('permission')
            ->first();

        $this->notSeeInDatabase('permission_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);

        $this->roleService->linkPermissionIdRole(
            $permission->id,
            $this->role
        );

        $this->seeInDatabase('permission_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);
    }
}

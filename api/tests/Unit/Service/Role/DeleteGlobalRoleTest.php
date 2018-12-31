<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();

        $permissions = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionGlobalRole')->create([
                'role_id' => $this->globalRole->id,
                'permission_id' => $permissionId
            ]);
        }

        factory('App\Model\GlobalRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->globalRole->id
        ]);
    }

    public function testDeleteGlobalRole(): void
    {
        $result = $this->roleService->deleteGlobalRole($this->globalRole->id);

        $this->assertEquals($this->globalRole->name, $result->name);
        $this->assertEquals($this->globalRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->globalRole->en_description, $result->en_description);
        $this->assertEquals($this->globalRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->globalRole->fr_description, $result->fr_description);
    }
}

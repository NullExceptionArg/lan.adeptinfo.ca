<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lanRole;
    protected $lan;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);

        $permissions = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        foreach ($permissions as $permissionId) {
            factory('App\Model\PermissionLanRole')->create([
                'role_id'       => $this->lanRole->id,
                'permission_id' => $permissionId,
            ]);
        }

        factory('App\Model\LanRoleUser')->create([
            'user_id' => $this->user->id,
            'role_id' => $this->lanRole->id,
        ]);
    }

    public function testDeleteLanRole(): void
    {
        $result = $this->roleService->deleteLanRole($this->lanRole->id);

        $this->assertEquals($this->lanRole->name, $result->name);
        $this->assertEquals($this->lanRole->en_display_name, $result->en_display_name);
        $this->assertEquals($this->lanRole->en_description, $result->en_description);
        $this->assertEquals($this->lanRole->fr_display_name, $result->fr_display_name);
        $this->assertEquals($this->lanRole->fr_description, $result->fr_description);
    }
}

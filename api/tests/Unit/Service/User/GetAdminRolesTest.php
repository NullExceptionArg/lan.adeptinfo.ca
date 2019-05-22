<?php

namespace Tests\Unit\Service\User;

use App\Model\Permission;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetAdminRolesTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->be($this->user);
    }

    public function testGetAdminRolesCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id,
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $lanRoles[$i]->id,
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id,
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id'       => $globalRoles[$i - 4]->id,
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id,
            ]);
        }

        $results = $this->userService->getAdminRoles($this->user->email, $lan->id)->jsonSerialize();

        $this->assertEquals($globalRoles[0]->id, $results['global_roles'][0]->id);
        $this->assertEquals($globalRoles[0]->name, $results['global_roles'][0]->name);
        $this->assertEquals($globalRoles[0]->en_display_name, $results['global_roles'][0]->en_display_name);
        $this->assertEquals($globalRoles[0]->en_description, $results['global_roles'][0]->en_description);

        $this->assertEquals($globalRoles[1]->id, $results['global_roles'][1]->id);
        $this->assertEquals($globalRoles[1]->name, $results['global_roles'][1]->name);
        $this->assertEquals($globalRoles[1]->en_display_name, $results['global_roles'][1]->en_display_name);
        $this->assertEquals($globalRoles[1]->en_description, $results['global_roles'][1]->en_description);

        $this->assertEquals($globalRoles[2]->id, $results['global_roles'][2]->id);
        $this->assertEquals($globalRoles[2]->name, $results['global_roles'][2]->name);
        $this->assertEquals($globalRoles[2]->en_display_name, $results['global_roles'][2]->en_display_name);
        $this->assertEquals($globalRoles[2]->en_description, $results['global_roles'][2]->en_description);

        $this->assertEquals($globalRoles[3]->id, $results['global_roles'][3]->id);
        $this->assertEquals($globalRoles[3]->name, $results['global_roles'][3]->name);
        $this->assertEquals($globalRoles[3]->en_display_name, $results['global_roles'][3]->en_display_name);
        $this->assertEquals($globalRoles[3]->en_description, $results['global_roles'][3]->en_description);

        $this->assertEquals($lanRoles[0]->id, $results['lan_roles'][0]->id);
        $this->assertEquals($lanRoles[0]->name, $results['lan_roles'][0]->name);
        $this->assertEquals($lanRoles[0]->en_display_name, $results['lan_roles'][0]->en_display_name);
        $this->assertEquals($lanRoles[0]->en_description, $results['lan_roles'][0]->en_description);

        $this->assertEquals($lanRoles[1]->id, $results['lan_roles'][1]->id);
        $this->assertEquals($lanRoles[1]->name, $results['lan_roles'][1]->name);
        $this->assertEquals($lanRoles[1]->en_display_name, $results['lan_roles'][1]->en_display_name);
        $this->assertEquals($lanRoles[1]->en_description, $results['lan_roles'][1]->en_description);

        $this->assertEquals($lanRoles[2]->id, $results['lan_roles'][2]->id);
        $this->assertEquals($lanRoles[2]->name, $results['lan_roles'][2]->name);
        $this->assertEquals($lanRoles[2]->en_display_name, $results['lan_roles'][2]->en_display_name);
        $this->assertEquals($lanRoles[2]->en_description, $results['lan_roles'][2]->en_description);

        $this->assertEquals($lanRoles[3]->id, $results['lan_roles'][3]->id);
        $this->assertEquals($lanRoles[3]->name, $results['lan_roles'][3]->name);
        $this->assertEquals($lanRoles[3]->en_display_name, $results['lan_roles'][3]->en_display_name);
        $this->assertEquals($lanRoles[3]->en_description, $results['lan_roles'][3]->en_description);
    }
}

<?php

namespace Tests\Unit\Service\User;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetAdminRoles extends TestCase
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

    public function testGetAdminRolesUserBeingCheckedFrench(): void
    {
        $userBeingChecked = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $request = new Request([
            'lan_id' => $lan->id,
            'email' => $userBeingChecked->email,
            'lang' => 'fr'
        ]);

        $results = $this->userService->getAdminRoles($request)->jsonSerialize();

        $this->assertEquals($globalRoles[0]->id, $results['global_roles'][0]->id);
        $this->assertEquals($globalRoles[0]->name, $results['global_roles'][0]->name);
        $this->assertEquals($globalRoles[0]->fr_display_name, $results['global_roles'][0]->fr_display_name);
        $this->assertEquals($globalRoles[0]->fr_description, $results['global_roles'][0]->fr_description);

        $this->assertEquals($globalRoles[1]->id, $results['global_roles'][1]->id);
        $this->assertEquals($globalRoles[1]->name, $results['global_roles'][1]->name);
        $this->assertEquals($globalRoles[1]->fr_display_name, $results['global_roles'][1]->fr_display_name);
        $this->assertEquals($globalRoles[1]->fr_description, $results['global_roles'][1]->fr_description);

        $this->assertEquals($globalRoles[2]->id, $results['global_roles'][2]->id);
        $this->assertEquals($globalRoles[2]->name, $results['global_roles'][2]->name);
        $this->assertEquals($globalRoles[2]->fr_display_name, $results['global_roles'][2]->fr_display_name);
        $this->assertEquals($globalRoles[2]->fr_description, $results['global_roles'][2]->fr_description);

        $this->assertEquals($globalRoles[3]->id, $results['global_roles'][3]->id);
        $this->assertEquals($globalRoles[3]->name, $results['global_roles'][3]->name);
        $this->assertEquals($globalRoles[3]->fr_display_name, $results['global_roles'][3]->fr_display_name);
        $this->assertEquals($globalRoles[3]->fr_description, $results['global_roles'][3]->fr_description);

        $this->assertEquals($lanRoles[0]->id, $results['lan_roles'][0]->id);
        $this->assertEquals($lanRoles[0]->name, $results['lan_roles'][0]->name);
        $this->assertEquals($lanRoles[0]->fr_display_name, $results['lan_roles'][0]->fr_display_name);
        $this->assertEquals($lanRoles[0]->fr_description, $results['lan_roles'][0]->fr_description);

        $this->assertEquals($lanRoles[1]->id, $results['lan_roles'][1]->id);
        $this->assertEquals($lanRoles[1]->name, $results['lan_roles'][1]->name);
        $this->assertEquals($lanRoles[1]->fr_display_name, $results['lan_roles'][1]->fr_display_name);
        $this->assertEquals($lanRoles[1]->fr_description, $results['lan_roles'][1]->fr_description);

        $this->assertEquals($lanRoles[2]->id, $results['lan_roles'][2]->id);
        $this->assertEquals($lanRoles[2]->name, $results['lan_roles'][2]->name);
        $this->assertEquals($lanRoles[2]->fr_display_name, $results['lan_roles'][2]->fr_display_name);
        $this->assertEquals($lanRoles[2]->fr_description, $results['lan_roles'][2]->fr_description);

        $this->assertEquals($lanRoles[3]->id, $results['lan_roles'][3]->id);
        $this->assertEquals($lanRoles[3]->name, $results['lan_roles'][3]->name);
        $this->assertEquals($lanRoles[3]->fr_display_name, $results['lan_roles'][3]->fr_display_name);
        $this->assertEquals($lanRoles[3]->fr_description, $results['lan_roles'][3]->fr_description);
    }

    public function testGetAdminRolesUserBeingCheckedEnglish(): void
    {
        $userBeingChecked = factory('App\Model\User')->create();
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $userBeingChecked->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $request = new Request([
            'lan_id' => $lan->id,
            'email' => $userBeingChecked->email,
            'lang' => 'en'
        ]);

        $results = $this->userService->getAdminRoles($request)->jsonSerialize();

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

    public function testGetAdminRolesCurrentUser(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $request = new Request([
            'lan_id' => $lan->id,
            'email' => $this->user->email,
            'lang' => 'en'
        ]);

        $results = $this->userService->getAdminRoles($request)->jsonSerialize();

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

    public function testGetAdminRolesCurrentUserNoEmail(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $request = new Request([
            'lan_id' => $lan->id,
            'lang' => 'en'
        ]);

        $results = $this->userService->getAdminRoles($request)->jsonSerialize();

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

    public function testGetAdminRolesCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $permissions = Permission::inRandomOrder()
            ->where('name', '!=', 'get-admin-roles')
            ->take(8)
            ->get();

        $lanRoles = factory('App\Model\LanRole', 4)->create([
            'lan_id' => $lan->id
        ]);
        for ($i = 0; $i <= 3; $i++) {
            factory('App\Model\PermissionLanRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $lanRoles[$i]->id
            ]);
            factory('App\Model\LanRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $lanRoles[$i]->id
            ]);
        }

        $globalRoles = factory('App\Model\GlobalRole', 4)->create();
        for ($i = 4; $i <= 7; $i++) {
            factory('App\Model\PermissionGlobalRole')->create([
                'permission_id' => $permissions[$i]->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
            factory('App\Model\GlobalRoleUser')->create([
                'user_id' => $this->user->id,
                'role_id' => $globalRoles[$i - 4]->id
            ]);
        }

        $request = new Request([
            'email' => $this->user->email,
            'lang' => 'en'
        ]);

        $results = $this->userService->getAdminRoles($request)->jsonSerialize();

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

    public function testGetAdminRolesHasPermission(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $admin = factory('App\Model\User')->create();
        $this->be($admin);
        $request = new Request([
            'lan_id' => $lan->id,
            'email' => $this->user->email
        ]);
        try {
            $this->userService->getAdminRoles($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testGetAdminRolesLanIdExist(): void
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->userService->getAdminRoles($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testGetAdminRolesLanIdInteger(): void
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->userService->getAdminRoles($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testGetAdminRolesEmailExist(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $request = new Request([
            'email' => '☭',
            'lan_id' => $lan->id
        ]);
        try {
            $this->userService->getAdminRoles($request);
            $this->fail('Expected: {"email":["The selected email is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The selected email is invalid."]}', $e->getMessage());
        }
    }

    public function testGetAdminRolesEmailString(): void
    {
        $lan = factory('App\Model\Lan')->create();
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'get-admin-roles')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $request = new Request([
            'email' => 1,
            'lan_id' => $lan->id
        ]);
        try {
            $this->userService->getAdminRoles($request);
            $this->fail('Expected: {"email":["The email must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email must be a string."]}', $e->getMessage());
        }
    }

}

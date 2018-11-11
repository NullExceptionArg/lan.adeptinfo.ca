<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class EditLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lan;
    protected $lanRole;

    protected $paramsContent = [
        'role_id' => null,
        'name' => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description' => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description' => 'Notre égal.',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'edit-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->paramsContent['role_id'] = $this->lanRole->id;

        $this->be($this->user);
    }

    public function testEditLanRole(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->roleService->editLanRole($request);

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['en_display_name'], $result->en_display_name);
        $this->assertEquals($this->paramsContent['en_description'], $result->en_description);
        $this->assertEquals($this->paramsContent['fr_display_name'], $result->fr_display_name);
        $this->assertEquals($this->paramsContent['fr_description'], $result->fr_description);
    }

    public function testEditLanRoleLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testEditLanRoleRoleIdRequired(): void
    {
        $this->paramsContent['role_id'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"role_id":["The role id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The role id field is required."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleRoleIdExist(): void
    {
        $this->paramsContent['role_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"role_id":["The selected role id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"role_id":["The selected role id is invalid."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleNameString(): void
    {
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleNameMaxLength(): void
    {
        $this->paramsContent['name'] = str_repeat('☭', 51);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"name":["The name may not be greater than 50 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleNameUnique(): void
    {
        factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
            'name' => $this->paramsContent['name']
        ]);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"name":["The name has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name has already been taken."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleEnDisplayNameString(): void
    {
        $this->paramsContent['en_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name must be a string."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleEnDisplayNameMaxLength(): void
    {
        $this->paramsContent['en_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleEnDescriptionString(): void
    {
        $this->paramsContent['en_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"en_description":["The en description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description must be a string."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleEnDescriptionMaxLength(): void
    {
        $this->paramsContent['en_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"en_description":["The en description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleFrDisplayNameString(): void
    {
        $this->paramsContent['fr_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name must be a string."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleFrDisplayNameMaxLength(): void
    {
        $this->paramsContent['fr_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleFrDescriptionString(): void
    {
        $this->paramsContent['fr_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"fr_description":["The fr description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description must be a string."]}', $e->getMessage());
        }
    }

    public function testEditLanRoleFrDescriptionMaxLength(): void
    {
        $this->paramsContent['fr_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->editLanRole($request);
            $this->fail('Expected: {"fr_description":["The fr description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

}

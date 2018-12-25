<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;

    protected $paramsContent = [
        'name' => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description' => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description' => 'Notre égal.',
        'permissions' => null
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'create-global-role')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->paramsContent['permissions'] = Permission::inRandomOrder()
            ->take(10)
            ->pluck('id')
            ->toArray();

        $this->be($this->user);
    }

    public function testCreateGlobalRole(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->roleService->createGlobalRole($request);

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['en_display_name'], $result->en_display_name);
        $this->assertEquals($this->paramsContent['en_description'], $result->en_description);
        $this->assertEquals($this->paramsContent['fr_display_name'], $result->fr_display_name);
        $this->assertEquals($this->paramsContent['fr_description'], $result->fr_description);
    }

    public function testCreateGlobalRoleLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleNameRequired(): void
    {
        $this->paramsContent['name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleNameString(): void
    {
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleNameMaxLength(): void
    {
        $this->paramsContent['name'] = str_repeat('☭', 51);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"name":["The name may not be greater than 50 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleNameUnique(): void
    {
        factory('App\Model\GlobalRole')->create([
            'name' => $this->paramsContent['name']
        ]);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"name":["The name has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name has already been taken."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDisplayNameRequired(): void
    {
        $this->paramsContent['en_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDisplayNameString(): void
    {
        $this->paramsContent['en_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDisplayNameMaxLength(): void
    {
        $this->paramsContent['en_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDescriptionRequired(): void
    {
        $this->paramsContent['en_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_description":["The en description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDescriptionString(): void
    {
        $this->paramsContent['en_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_description":["The en description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleEnDescriptionMaxLength(): void
    {
        $this->paramsContent['en_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"en_description":["The en description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDisplayNameRequired(): void
    {
        $this->paramsContent['fr_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDisplayNameString(): void
    {
        $this->paramsContent['fr_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDisplayNameMaxLength(): void
    {
        $this->paramsContent['fr_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDescriptionRequired(): void
    {
        $this->paramsContent['fr_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_description":["The fr description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDescriptionString(): void
    {
        $this->paramsContent['fr_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_description":["The fr description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRoleFrDescriptionMaxLength(): void
    {
        $this->paramsContent['fr_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"fr_description":["The fr description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = [(string)$this->paramsContent['permissions'][0], $this->paramsContent['permissions'][1]];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testCreateGlobalRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->paramsContent['permissions'] = [$this->paramsContent['permissions'][0], -1];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createGlobalRole($request);
            $this->fail('Expected: {"permissions":["An element of the array is not an existing permission id."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["An element of the array is not an existing permission id."]}', $e->getMessage());
        }
    }
}

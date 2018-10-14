<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $lan;

    protected $paramsContent = [
        'lan_id' => null,
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
        $this->lan = factory('App\Model\Lan')->create();

        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['permissions'] = Permission::inRandomOrder()
            ->where('can_be_per_lan', true)
            ->take(5)
            ->pluck('id')
            ->toArray();

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
        $permission = Permission::where('name', 'create-lan-role')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testCreateLanRoleTest(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->roleService->createLanRole($request);

        $this->assertEquals($this->paramsContent['lan_id'], $result->lan_id);
        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['en_display_name'], $result->en_display_name);
        $this->assertEquals($this->paramsContent['en_description'], $result->en_description);
        $this->assertEquals($this->paramsContent['fr_display_name'], $result->fr_display_name);
        $this->assertEquals($this->paramsContent['fr_description'], $result->fr_description);
    }

    public function testCreateLanRoleLanHasPermission(): void
    {
        $user = $this->user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testCreateLanRoleLanIdExists(): void
    {
        $this->paramsContent['lan_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleLanIdInteger(): void
    {
        $this->paramsContent['lan_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleNameRequired(): void
    {
        $this->paramsContent['name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleNameString(): void
    {
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleNameMaxLength(): void
    {
        $this->paramsContent['name'] = str_repeat('☭', 51);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"name":["The name may not be greater than 50 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleNameUnique(): void
    {
        factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
            'name' => $this->paramsContent['name']
        ]);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"name":["The name has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name has already been taken."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDisplayNameRequired(): void
    {
        $this->paramsContent['en_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDisplayNameString(): void
    {
        $this->paramsContent['en_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDisplayNameMaxLength(): void
    {
        $this->paramsContent['en_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_display_name":["The en display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDescriptionRequired(): void
    {
        $this->paramsContent['en_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_description":["The en description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDescriptionString(): void
    {
        $this->paramsContent['en_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_description":["The en description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleEnDescriptionMaxLength(): void
    {
        $this->paramsContent['en_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"en_description":["The en description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDisplayNameRequired(): void
    {
        $this->paramsContent['fr_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDisplayNameString(): void
    {
        $this->paramsContent['fr_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDisplayNameMaxLength(): void
    {
        $this->paramsContent['fr_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDescriptionRequired(): void
    {
        $this->paramsContent['fr_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_description":["The fr description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDescriptionString(): void
    {
        $this->paramsContent['fr_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_description":["The fr description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateLanRoleFrDescriptionMaxLength(): void
    {
        $this->paramsContent['fr_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"fr_description":["The fr description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testCreateLanRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = [(string)$this->paramsContent['permissions'][0], $this->paramsContent['permissions'][1]];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testCreateLanRolePermissionCanBePerLan(): void
    {
        $permission = Permission::where('can_be_per_lan', false)->first();
        $this->paramsContent['permissions'] = [intval($permission->id)];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"permissions":["One of the provided permissions cannot be attributed to a LAN role."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["One of the provided permissions cannot be attributed to a LAN role."]}', $e->getMessage());
        }
    }

    public function testCreateLanRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->paramsContent['permissions'] = [$this->paramsContent['permissions'][0], -1];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->createLanRole($request);
            $this->fail('Expected: {"permissions":["An element of the array is not contained an existing permission id."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["An element of the array is not contained an existing permission id."]}', $e->getMessage());
        }
    }
}

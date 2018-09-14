<?php

namespace Tests\Unit\Service\Role;

use App\Model\Permission;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateTest extends TestCase
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
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testCreateRoleTest(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->roleService->create($request);

        $this->assertEquals($this->paramsContent['lan_id'], $result->lan_id);
        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['en_display_name'], $result->en_display_name);
        $this->assertEquals($this->paramsContent['en_description'], $result->en_description);
        $this->assertEquals($this->paramsContent['fr_display_name'], $result->fr_display_name);
        $this->assertEquals($this->paramsContent['fr_description'], $result->fr_description);
    }

    public function testCreateRoleLanIdExists(): void
    {
        $this->paramsContent['lan_id'] = -1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testCreateRoleLanIdInteger(): void
    {
        $this->paramsContent['lan_id'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateRoleNameRequired(): void
    {
        $this->paramsContent['name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRoleNameString(): void
    {
        $this->paramsContent['name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"name":["The name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateRoleNameMaxLength(): void
    {
        $this->paramsContent['name'] = str_repeat('☭', 51);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"name":["The name may not be greater than 50 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 50 characters."]}', $e->getMessage());
        }
    }

    public function testCreateRoleNameUnique(): void
    {
        factory('App\Model\Role')->create([
            'lan_id' => $this->lan->id,
            'name' => $this->paramsContent['name']
        ]);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"name":["The name has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name has already been taken."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDisplayNameRequired(): void
    {
        $this->paramsContent['en_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_display_name":["The en display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDisplayNameString(): void
    {
        $this->paramsContent['en_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_display_name":["The en display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDisplayNameMaxLength(): void
    {
        $this->paramsContent['en_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_display_name":["The en display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_display_name":["The en display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDescriptionRequired(): void
    {
        $this->paramsContent['en_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_description":["The en description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDescriptionString(): void
    {
        $this->paramsContent['en_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_description":["The en description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateRoleEnDescriptionMaxLength(): void
    {
        $this->paramsContent['en_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"en_description":["The en description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"en_description":["The en description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDisplayNameRequired(): void
    {
        $this->paramsContent['fr_display_name'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDisplayNameString(): void
    {
        $this->paramsContent['fr_display_name'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDisplayNameMaxLength(): void
    {
        $this->paramsContent['fr_display_name'] = str_repeat('☭', 71);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_display_name":["The fr display name may not be greater than 70 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_display_name":["The fr display name may not be greater than 70 characters."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDescriptionRequired(): void
    {
        $this->paramsContent['fr_description'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_description":["The fr description field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDescriptionString(): void
    {
        $this->paramsContent['fr_description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_description":["The fr description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description must be a string."]}', $e->getMessage());
        }
    }

    public function testCreateRoleFrDescriptionMaxLength(): void
    {
        $this->paramsContent['fr_description'] = str_repeat('☭', 1001);
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"fr_description":["The fr description may not be greater than 1000 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"fr_description":["The fr description may not be greater than 1000 characters."]}', $e->getMessage());
        }
    }

    public function testCreateRolePermissionsRequired(): void
    {
        $this->paramsContent['permissions'] = null;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"permissions":["The permissions field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions field is required."]}', $e->getMessage());
        }
    }

    public function testCreateRolePermissionsArray(): void
    {
        $this->paramsContent['permissions'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"permissions":["The permissions must be an array."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The permissions must be an array."]}', $e->getMessage());
        }
    }

    public function testCreateRolePermissionsArrayOfInteger(): void
    {
        $this->paramsContent['permissions'] = ['1', 2];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"permissions":["The array must contain only integers."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["The array must contain only integers."]}', $e->getMessage());
        }
    }

    public function testCreateRolePermissionsElementsInArrayExistInPermission(): void
    {
        $this->paramsContent['permissions'] = [2, -1];
        $request = new Request($this->paramsContent);
        try {
            $this->roleService->create($request);
            $this->fail('Expected: {"permissions":["An element of the array is not contained an existing permission id."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"permissions":["An element of the array is not contained an existing permission id."]}', $e->getMessage());
        }
    }
}

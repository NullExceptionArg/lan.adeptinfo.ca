<?php

namespace Tests\Unit\Service\Lan;

use App\Model\Permission;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class SetCurrentTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanService;

    protected $user;
    protected $lan;

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', 'set-current-lan')->first();
        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->be($this->user);
    }

    public function testSetCurrentNoCurrentLan()
    {
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->lanService->setCurrent($request);

        $this->assertEquals($this->lan->id, $result);
    }

    public function testSetCurrentHasCurrentLan()
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        $request = new Request([
            'lan_id' => $lan->id
        ]);
        $result = $this->lanService->setCurrent($request);

        $this->assertEquals($lan->id, $result);
    }

    public function testSetCurrentHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->be($user);
        $request = new Request([
            'lan_id' => $this->lan->id
        ]);
        try {
            $this->lanService->setCurrent($request);
            $this->fail('Expected: REEEEEEEEEE');
        } catch (AuthorizationException $e) {
            $this->assertEquals('REEEEEEEEEE', $e->getMessage());
        }
    }

    public function testSetCurrentIdExist()
    {
        $request = new Request([
            'lan_id' => -1
        ]);
        try {
            $this->lanService->setCurrent($request);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testSetCurrentIdInteger()
    {
        $request = new Request([
            'lan_id' => '☭'
        ]);
        try {
            $this->lanService->setCurrent($request);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }
}

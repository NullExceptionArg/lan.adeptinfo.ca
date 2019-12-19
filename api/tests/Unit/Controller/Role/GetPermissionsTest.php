<?php

namespace Tests\Unit\Controller\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPermissionsTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory('App\Model\User')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'get-permissions'
        );
    }

    public function testGetPermissions(): void
    {
        $response = $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/role/permissions');
        $response
            ->seeJsonStructure([[
                'id', 'name', 'can_be_per_lan', 'display_name', 'description',
            ]])
            ->assertResponseStatus(200);
        $this->assertEquals(count(include base_path().'/resources/permissions.php'), count(json_decode($this->response->content())));
    }

    public function testGetPermissionsLanHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/role/permissions')
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }
}

<?php

namespace Tests\Unit\Repository\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinkPermissionIdLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $lan;
    protected $role;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->role = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testLinkPermissionIdLanRole(): void
    {
        $permission = DB::table('permission')
            ->first();

        $this->notSeeInDatabase('permission_lan_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);

        $this->roleRepository->linkPermissionIdLanRole(
            $permission->id,
            $this->role
        );

        $this->seeInDatabase('permission_lan_role', [
            'permission_id' => $permission->id,
            'role_id' => $this->role->id
        ]);
    }
}

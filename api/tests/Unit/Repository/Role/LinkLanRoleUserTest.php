<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class LinkLanRoleUserTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $lan;
    protected $lanRole;

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testLinkLanRoleUser(): void
    {
        $this->notSeeInDatabase('lan_role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->lanRole->id,
        ]);

        $this->roleRepository->linkLanRoleUser(
            $this->lanRole->id,
            $this->user->id
        );

        $this->seeInDatabase('lan_role_user', [
            'user_id' => $this->user->id,
            'role_id' => $this->lanRole->id,
        ]);
    }
}

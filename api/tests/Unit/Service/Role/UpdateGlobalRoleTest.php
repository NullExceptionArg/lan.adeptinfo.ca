<?php

namespace Tests\Unit\Service\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleService;

    protected $user;
    protected $globalRole;

    protected $paramsContent = [
        'name'            => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description'  => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description'  => 'Notre égal.',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleService = $this->app->make('App\Services\Implementation\RoleServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->globalRole = factory('App\Model\GlobalRole')->create();
    }

    public function testUpdateGlobalRole(): void
    {
        $result = $this->roleService->updateGlobalRole(
            $this->globalRole->id,
            $this->paramsContent['name'],
            $this->paramsContent['en_display_name'],
            $this->paramsContent['en_description'],
            $this->paramsContent['fr_display_name'],
            $this->paramsContent['fr_description']
        );

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['en_display_name'], $result->en_display_name);
        $this->assertEquals($this->paramsContent['en_description'], $result->en_description);
        $this->assertEquals($this->paramsContent['fr_display_name'], $result->fr_display_name);
        $this->assertEquals($this->paramsContent['fr_description'], $result->fr_description);
    }
}

<?php

namespace Tests\Unit\Repository\Role;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $lan;
    protected $lanRole;

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
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->lanRole = factory('App\Model\LanRole')->create([
            'lan_id' => $this->lan->id,
        ]);
    }

    public function testUpdateLanRole(): void
    {
        $this->seeInDatabase('lan_role', [
            'name'            => $this->lanRole->name,
            'en_display_name' => $this->lanRole->en_display_name,
            'en_description'  => $this->lanRole->en_description,
            'fr_display_name' => $this->lanRole->fr_display_name,
            'fr_description'  => $this->lanRole->fr_description,
            'lan_id'          => $this->lan->id,
        ]);

        $this->roleRepository->updateLanRole(
            $this->lanRole->id,
            $this->paramsContent['name'],
            $this->paramsContent['en_display_name'],
            $this->paramsContent['en_description'],
            $this->paramsContent['fr_display_name'],
            $this->paramsContent['fr_description']
        );

        $this->seeInDatabase('lan_role', [
            'name'            => $this->paramsContent['name'],
            'en_display_name' => $this->paramsContent['en_display_name'],
            'en_description'  => $this->paramsContent['en_description'],
            'fr_display_name' => $this->paramsContent['fr_display_name'],
            'fr_description'  => $this->paramsContent['fr_description'],
            'lan_id'          => $this->lan->id,
        ]);
    }
}

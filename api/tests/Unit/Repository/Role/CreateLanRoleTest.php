<?php

namespace Tests\Unit\Repository\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateLanRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;
    protected $lan;

    protected $paramsContent = [
        'lan_id' => null,
        'name' => 'comrade',
        'en_display_name' => 'Comrade',
        'en_description' => 'Our equal',
        'fr_display_name' => 'Camarade',
        'fr_description' => 'Notre égal.',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->roleRepository = $this->app->make('App\Repositories\Implementation\RoleRepositoryImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->paramsContent['lan_id'] = $this->lan->id;
        $this->paramsContent['permissions'] = DB::table('permission')
            ->inRandomOrder()
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testCreateLanRole(): void
    {
        $this->notSeeInDatabase('lan_role', [
            'lan_id' => $this->paramsContent['lan_id'],
            'name' => $this->paramsContent['name'],
            'en_display_name' => $this->paramsContent['en_display_name'],
            'en_description' => $this->paramsContent['en_description'],
            'fr_display_name' => $this->paramsContent['fr_display_name'],
            'fr_description' => $this->paramsContent['fr_description']
        ]);

        $this->roleRepository->createLanRole(
            $this->paramsContent['lan_id'],
            $this->paramsContent['name'],
            $this->paramsContent['en_display_name'],
            $this->paramsContent['en_description'],
            $this->paramsContent['fr_display_name'],
            $this->paramsContent['fr_description']
        );

        $this->seeInDatabase('lan_role', [
            'lan_id' => $this->paramsContent['lan_id'],
            'name' => $this->paramsContent['name'],
            'en_display_name' => $this->paramsContent['en_display_name'],
            'en_description' => $this->paramsContent['en_description'],
            'fr_display_name' => $this->paramsContent['fr_display_name'],
            'fr_description' => $this->paramsContent['fr_description']
        ]);
    }
}

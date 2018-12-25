<?php

namespace Tests\Unit\Repository\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateGlobalRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $roleRepository;

    protected $user;

    protected $paramsContent = [
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

        $this->paramsContent['permissions'] = DB::table('permission')
            ->inRandomOrder()
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testCreateGlobalRole(): void
    {
        $this->notSeeInDatabase('global_role', [
            'name' => $this->paramsContent['name'],
            'en_display_name' => $this->paramsContent['en_display_name'],
            'en_description' => $this->paramsContent['en_description'],
            'fr_display_name' => $this->paramsContent['fr_display_name'],
            'fr_description' => $this->paramsContent['fr_description']
        ]);

        $this->roleRepository->createGlobalRole(
            $this->paramsContent['name'],
            $this->paramsContent['en_display_name'],
            $this->paramsContent['en_description'],
            $this->paramsContent['fr_display_name'],
            $this->paramsContent['fr_description']
        );

        $this->seeInDatabase('global_role', [
            'name' => $this->paramsContent['name'],
            'en_display_name' => $this->paramsContent['en_display_name'],
            'en_description' => $this->paramsContent['en_description'],
            'fr_display_name' => $this->paramsContent['fr_display_name'],
            'fr_description' => $this->paramsContent['fr_description']
        ]);
    }
}

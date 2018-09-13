<?php

namespace Tests\Unit\Controller\Role;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateRoleTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
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

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->requestContent['lan_id'] = $this->lan->id;
        $this->requestContent['permissions'] = DB::inRandomOrder()
            ->take(5)
            ->pluck('id')
            ->toArray();
    }

    public function testCreateRoleTest(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'name' => $this->requestContent['name'],
                'lan_start' => $this->requestContent['lan_start'],
            ])
            ->assertResponseStatus(201);
    }
}

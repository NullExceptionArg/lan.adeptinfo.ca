<?php

namespace Tests\Unit\Controller\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $users;
    protected $user;
    protected $lan;

    protected $requestContent = [
        'query_string'    => '',
        'order_column'    => 'first_name',
        'order_direction' => 'desc',
        'items_per_page'  => 2,
        'current_page'    => 1,
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->lan = factory('App\Model\Lan')->create([
            'is_current' => true,
        ]);

        $this->user = factory('App\Model\User')->create();

        $this->users[0] = factory('App\Model\User')->create([
            'first_name' => 'Karl',
            'last_name'  => 'Marx',
            'email'      => 'karl.marx@unite.org',
        ]);

        $this->users[1] = factory('App\Model\User')->create([
            'first_name' => 'Vladimir',
            'last_name'  => 'Lenin',
            'email'      => 'vlad.lenin@unite.org',
        ]);

        $this->users[2] = factory('App\Model\User')->create([
            'first_name' => 'Leon',
            'last_name'  => 'Trotsky',
            'email'      => 'leon.trotsky@unite.org',
        ]);

        $this->users[3] = factory('App\Model\User')->create([
            'first_name' => 'Joseph',
            'last_name'  => 'Stalin',
            'email'      => 'joseph.stalin@unite.org',
        ]);

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'get-users'
        );
    }

    public function testGetUsers(): void
    {
        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Vladimir',
                        'last_name'  => 'Lenin',
                        'email'      => 'vlad.lenin@unite.org',
                    ],
                    [
                        'first_name' => 'Leon',
                        'last_name'  => 'Trotsky',
                        'email'      => 'leon.trotsky@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 2,
                    'per_page'     => 2,
                    'current_page' => 1,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersDefaults(): void
    {
        $this->requestContent['query_string'] = '';
        $this->requestContent['order_column'] = '';
        $this->requestContent['order_direction'] = '';
        $this->requestContent['items_per_page'] = '';
        $this->requestContent['current_page'] = '';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Vladimir',
                        'last_name'  => 'Lenin',
                        'email'      => 'vlad.lenin@unite.org',
                    ],
                    [
                        'first_name' => 'Karl',
                        'last_name'  => 'Marx',
                        'email'      => 'karl.marx@unite.org',
                    ],
                    [
                        'first_name' => 'Joseph',
                        'last_name'  => 'Stalin',
                        'email'      => 'joseph.stalin@unite.org',
                    ],
                    [
                        'first_name' => 'Leon',
                        'last_name'  => 'Trotsky',
                        'email'      => 'leon.trotsky@unite.org',
                    ],
                    [
                        'first_name' => $this->user->first_name,
                        'last_name'  => $this->user->last_name,
                        'email'      => $this->user->email,
                    ],
                ],
                'pagination' => [
                    'total'        => 5,
                    'count'        => 5,
                    'per_page'     => 15,
                    'current_page' => 1,
                    'total_pages'  => 1,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersQueryString(): void
    {
        $this->requestContent['query_string'] = 'Marx';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Karl',
                        'last_name'  => 'Marx',
                        'email'      => 'karl.marx@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 1,
                    'count'        => 1,
                    'per_page'     => 2,
                    'current_page' => 1,
                    'total_pages'  => 1,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersOrderColumn(): void
    {
        $this->requestContent['order_column'] = 'email';
        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Vladimir',
                        'last_name'  => 'Lenin',
                        'email'      => 'vlad.lenin@unite.org',
                    ],
                    [
                        'first_name' => 'Leon',
                        'last_name'  => 'Trotsky',
                        'email'      => 'leon.trotsky@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 2,
                    'per_page'     => 2,
                    'current_page' => 1,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersOrderDirection(): void
    {
        $this->requestContent['order_direction'] = 'asc';
        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Joseph',
                        'last_name'  => 'Stalin',
                        'email'      => 'joseph.stalin@unite.org',
                    ],
                    [
                        'first_name' => 'Karl',
                        'last_name'  => 'Marx',
                        'email'      => 'karl.marx@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 2,
                    'per_page'     => 2,
                    'current_page' => 1,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersItemsPerPage(): void
    {
        $this->requestContent['items_per_page'] = 3;
        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Vladimir',
                        'last_name'  => 'Lenin',
                        'email'      => 'vlad.lenin@unite.org',
                    ],
                    [
                        'first_name' => 'Leon',
                        'last_name'  => 'Trotsky',
                        'email'      => 'leon.trotsky@unite.org',
                    ],
                    [
                        'first_name' => 'Karl',
                        'last_name'  => 'Marx',
                        'email'      => 'karl.marx@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 3,
                    'per_page'     => 3,
                    'current_page' => 1,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersCurrentPage(): void
    {
        $this->requestContent['current_page'] = 2;
        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Karl',
                        'last_name'  => 'Marx',
                        'email'      => 'karl.marx@unite.org',
                    ],
                    [
                        'first_name' => 'Joseph',
                        'last_name'  => 'Stalin',
                        'email'      => 'joseph.stalin@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 2,
                    'per_page'     => 2,
                    'current_page' => 2,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersQueryStringMaxLength(): void
    {
        $this->requestContent['query_string'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'query_string' => [
                        0 => 'The query string may not be greater than 255 characters.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testGetUsersHasPermissionNotCurrentLan(): void
    {
        $user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->actingAs($user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 403,
                'message' => 'REEEEEEEEEE',
            ])
            ->assertResponseStatus(403);
    }

    public function testGetUsersHasPermissionGlobal(): void
    {
        $user = factory('App\Model\User')->create();

        $this->addGlobalPermissionToUser(
            $user->id,
            'get-users'
        );

        $this->requestContent['query_string'] = '@unite.org';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'data' => [
                    [
                        'first_name' => 'Vladimir',
                        'last_name'  => 'Lenin',
                        'email'      => 'vlad.lenin@unite.org',
                    ],
                    [
                        'first_name' => 'Leon',
                        'last_name'  => 'Trotsky',
                        'email'      => 'leon.trotsky@unite.org',
                    ],
                ],
                'pagination' => [
                    'total'        => 4,
                    'count'        => 2,
                    'per_page'     => 2,
                    'current_page' => 1,
                    'total_pages'  => 2,
                ],
            ])
            ->assertResponseStatus(200);
    }

    public function testGetUsersQueryStringString(): void
    {
        $this->requestContent['query_string'] = 1;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'query_string' => [
                        0 => 'The query string must be a string.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersOrderColumnRuleIn(): void
    {
        $this->requestContent['order_column'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'order_column' => [
                        0 => 'The selected order column is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersOrderDirectionRuleIn(): void
    {
        $this->requestContent['order_direction'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'order_direction' => [
                        0 => 'The selected order direction is invalid.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersItemsPerPageInteger(): void
    {
        $this->requestContent['items_per_page'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'items_per_page' => [
                        0 => 'The items per page must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersItemsPerPageMin(): void
    {
        $this->requestContent['items_per_page'] = 0;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'items_per_page' => [
                        0 => 'The items per page must be at least 1.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersItemsPerPageMax(): void
    {
        $this->requestContent['items_per_page'] = 76;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'items_per_page' => [
                        0 => 'The items per page may not be greater than 75.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersCurrentPageInteger(): void
    {
        $this->requestContent['current_page'] = '☭';
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'current_page' => [
                        0 => 'The current page must be an integer.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }

    public function testGetUsersCurrentPageMin(): void
    {
        $this->requestContent['current_page'] = 0;
        $this->actingAs($this->user)
            ->json('GET', 'http://'.env('API_DOMAIN').'/user', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status'  => 400,
                'message' => [
                    'current_page' => [
                        0 => 'The current page must be at least 1.',
                    ],
                ],
            ])
            ->assertResponseStatus(400);
    }
}

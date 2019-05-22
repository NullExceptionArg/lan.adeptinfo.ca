<?php

namespace Tests\Unit\Service\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $users;

    protected $paramsContent = [
        'query_string'    => '',
        'order_column'    => 'first_name',
        'order_direction' => 'desc',
        'items_per_page'  => 2,
        'current_page'    => 1,
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

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
    }

    public function testGetUsers(): void
    {
        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Vladimir', $result[0]->first_name);
        $this->assertEquals('Lenin', $result[0]->last_name);
        $this->assertEquals('vlad.lenin@unite.org', $result[0]->email);

        $this->assertEquals('Leon', $result[1]->first_name);
        $this->assertEquals('Trotsky', $result[1]->last_name);
        $this->assertEquals('leon.trotsky@unite.org', $result[1]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(2, $result->resource->count());
        $this->assertEquals(2, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(2, $result->resource->lastPage());
    }

    public function testGetUsersDefaults(): void
    {
        $this->paramsContent['query_string'] = null;
        $this->paramsContent['order_column'] = null;
        $this->paramsContent['order_direction'] = null;
        $this->paramsContent['items_per_page'] = null;
        $this->paramsContent['current_page'] = null;

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Vladimir', $result[0]->first_name);
        $this->assertEquals('Lenin', $result[0]->last_name);
        $this->assertEquals('vlad.lenin@unite.org', $result[0]->email);

        $this->assertEquals('Karl', $result[1]->first_name);
        $this->assertEquals('Marx', $result[1]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[1]->email);

        $this->assertEquals('Joseph', $result[2]->first_name);
        $this->assertEquals('Stalin', $result[2]->last_name);
        $this->assertEquals('joseph.stalin@unite.org', $result[2]->email);

        $this->assertEquals('Leon', $result[3]->first_name);
        $this->assertEquals('Trotsky', $result[3]->last_name);
        $this->assertEquals('leon.trotsky@unite.org', $result[3]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(4, $result->resource->count());
        $this->assertEquals(15, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(1, $result->resource->lastPage());
    }

    public function testGetUsersQueryString(): void
    {
        $this->paramsContent['query_string'] = 'ar';

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Karl', $result[0]->first_name);
        $this->assertEquals('Marx', $result[0]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[0]->email);

        $this->assertEquals(1, $result->resource->total());
        $this->assertEquals(1, $result->resource->count());
        $this->assertEquals(2, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(1, $result->resource->lastPage());
    }

    public function testGetUsersOrderColumn(): void
    {
        $this->paramsContent['order_column'] = 'email';

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Vladimir', $result[0]->first_name);
        $this->assertEquals('Lenin', $result[0]->last_name);
        $this->assertEquals('vlad.lenin@unite.org', $result[0]->email);

        $this->assertEquals('Leon', $result[1]->first_name);
        $this->assertEquals('Trotsky', $result[1]->last_name);
        $this->assertEquals('leon.trotsky@unite.org', $result[1]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(2, $result->resource->count());
        $this->assertEquals(2, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(2, $result->resource->lastPage());
    }

    public function testGetUsersOrderDirection(): void
    {
        $this->paramsContent['order_direction'] = 'asc';

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Joseph', $result[0]->first_name);
        $this->assertEquals('Stalin', $result[0]->last_name);
        $this->assertEquals('joseph.stalin@unite.org', $result[0]->email);

        $this->assertEquals('Karl', $result[1]->first_name);
        $this->assertEquals('Marx', $result[1]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[1]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(2, $result->resource->count());
        $this->assertEquals(2, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(2, $result->resource->lastPage());
    }

    public function testGetUsersItemsPerPage(): void
    {
        $this->paramsContent['items_per_page'] = 3;

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Vladimir', $result[0]->first_name);
        $this->assertEquals('Lenin', $result[0]->last_name);
        $this->assertEquals('vlad.lenin@unite.org', $result[0]->email);

        $this->assertEquals('Leon', $result[1]->first_name);
        $this->assertEquals('Trotsky', $result[1]->last_name);
        $this->assertEquals('leon.trotsky@unite.org', $result[1]->email);

        $this->assertEquals('Karl', $result[2]->first_name);
        $this->assertEquals('Marx', $result[2]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[2]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(3, $result->resource->count());
        $this->assertEquals(3, $result->resource->perPage());
        $this->assertEquals(1, $result->resource->currentPage());
        $this->assertEquals(2, $result->resource->lastPage());
    }

    public function testGetUsersCurrentPage(): void
    {
        $this->paramsContent['current_page'] = 2;

        $result = $this->userService->getUsers(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Karl', $result[0]->first_name);
        $this->assertEquals('Marx', $result[0]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[0]->email);

        $this->assertEquals('Joseph', $result[1]->first_name);
        $this->assertEquals('Stalin', $result[1]->last_name);
        $this->assertEquals('joseph.stalin@unite.org', $result[1]->email);

        $this->assertEquals(4, $result->resource->total());
        $this->assertEquals(2, $result->resource->count());
        $this->assertEquals(2, $result->resource->perPage());
        $this->assertEquals(2, $result->resource->currentPage());
        $this->assertEquals(2, $result->resource->lastPage());
    }
}

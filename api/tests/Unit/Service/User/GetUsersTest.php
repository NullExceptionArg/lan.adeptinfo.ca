<?php

namespace Tests\Unit\Service\User;

use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use DatabaseMigrations;

    protected $userService;

    protected $users;

    protected $paramsContent = [
        'query_string' => '',
        'order_column' => 'first_name',
        'order_direction' => 'desc',
        'items_per_page' => 2,
        'current_page' => 1
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');

        $this->users[0] = factory('App\Model\User')->create([
            'first_name' => 'Karl',
            'last_name' => 'Marx',
            'email' => 'karl.marx@unite.org',
        ]);

        $this->users[1] = factory('App\Model\User')->create([
            'first_name' => 'Vladimir',
            'last_name' => 'Lenin',
            'email' => 'vlad.lenin@unite.org',
        ]);

        $this->users[2] = factory('App\Model\User')->create([
            'first_name' => 'Leon',
            'last_name' => 'Trotsky',
            'email' => 'leon.trotsky@unite.org',
        ]);

        $this->users[3] = factory('App\Model\User')->create([
            'first_name' => 'Joseph',
            'last_name' => 'Stalin',
            'email' => 'joseph.stalin@unite.org',
        ]);
    }

    public function testGetUsers(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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
        $this->paramsContent['query_string'] = '';
        $this->paramsContent['order_column'] = '';
        $this->paramsContent['order_direction'] = '';
        $this->paramsContent['items_per_page'] = '';
        $this->paramsContent['current_page'] = '';

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

        $request = new Request($this->paramsContent);
        $result = $this->userService->getUsers($request);

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

    public function testGetUsersQueryStringMaxLength(): void
    {
        $this->paramsContent['query_string'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"query_string":["The query string may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"query_string":["The query string may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testGetUsersQueryStringString(): void
    {
        $this->paramsContent['query_string'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"query_string":["The query string must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"query_string":["The query string must be a string."]}', $e->getMessage());
        }
    }

    public function testGetUsersOrderColumnRuleIn(): void
    {
        $this->paramsContent['order_column'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"order_column":["The selected order column is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"order_column":["The selected order column is invalid."]}', $e->getMessage());
        }
    }

    public function testGetUsersOrderDirectionRuleIn(): void
    {
        $this->paramsContent['order_direction'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"order_direction":["The selected order direction is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"order_direction":["The selected order direction is invalid."]}', $e->getMessage());
        }
    }

    public function testGetUsersItemsPerPageNumeric(): void
    {
        $this->paramsContent['items_per_page'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"items_per_page":["The items per page must be a number."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"items_per_page":["The items per page must be a number."]}', $e->getMessage());
        }
    }

    public function testGetUsersItemsPerPageMin(): void
    {
        $this->paramsContent['items_per_page'] = 0;
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"items_per_page":["The items per page must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"items_per_page":["The items per page must be at least 1."]}', $e->getMessage());
        }
    }

    public function testGetUsersItemsPerPageMax(): void
    {
        $this->paramsContent['items_per_page'] = 76;
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"items_per_page":["The items per page may not be greater than 75."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"items_per_page":["The items per page may not be greater than 75."]}', $e->getMessage());
        }
    }

    public function testGetUsersCurrentPageNumeric(): void
    {
        $this->paramsContent['current_page'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"current_page":["The current page must be a number."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"current_page":["The current page must be a number."]}', $e->getMessage());
        }
    }

    public function testGetUsersCurrentPageMin(): void
    {
        $this->paramsContent['current_page'] = 0;
        $request = new Request($this->paramsContent);
        try {
            $this->userService->getUsers($request);
            $this->fail('Expected: {"current_page":["The current page must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"current_page":["The current page must be at least 1."]}', $e->getMessage());
        }
    }
}

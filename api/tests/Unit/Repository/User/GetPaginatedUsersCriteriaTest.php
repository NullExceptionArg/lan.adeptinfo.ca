<?php

namespace Tests\Unit\Repository\User;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class GetPaginatedUsersCriteriaTest extends TestCase
{
    use DatabaseMigrations;

    protected $userRepository;

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
        $this->userRepository = $this->app->make('App\Repositories\Implementation\UserRepositoryImpl');

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


    public function testGetPaginatedUsersCriteria(): void
    {
        $result = $this->userRepository->getPaginatedUsersCriteria(
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

        $this->assertEquals(4, $result->total());
        $this->assertEquals(2, $result->count());
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }

    public function testGetUsersQueryString(): void
    {
        $this->paramsContent['query_string'] = 'ar';

        $result = $this->userRepository->getPaginatedUsersCriteria(
            $this->paramsContent['query_string'],
            $this->paramsContent['order_column'],
            $this->paramsContent['order_direction'],
            $this->paramsContent['items_per_page'],
            $this->paramsContent['current_page']
        );

        $this->assertEquals('Karl', $result[0]->first_name);
        $this->assertEquals('Marx', $result[0]->last_name);
        $this->assertEquals('karl.marx@unite.org', $result[0]->email);

        $this->assertEquals(1, $result->total());
        $this->assertEquals(1, $result->count());
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(1, $result->lastPage());
    }

    public function testGetUsersOrderColumn(): void
    {
        $this->paramsContent['order_column'] = 'email';

        $result = $this->userRepository->getPaginatedUsersCriteria(
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

        $this->assertEquals(4, $result->total());
        $this->assertEquals(2, $result->count());
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }

    public function testGetUsersOrderDirection(): void
    {
        $this->paramsContent['order_direction'] = 'asc';

        $result = $this->userRepository->getPaginatedUsersCriteria(
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

        $this->assertEquals(4, $result->total());
        $this->assertEquals(2, $result->count());
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }

    public function testGetUsersItemsPerPage(): void
    {
        $this->paramsContent['items_per_page'] = 3;

        $result = $this->userRepository->getPaginatedUsersCriteria(
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

        $this->assertEquals(4, $result->total());
        $this->assertEquals(3, $result->count());
        $this->assertEquals(3, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }

    public function testGetUsersCurrentPage(): void
    {
        $this->paramsContent['current_page'] = 2;

        $result = $this->userRepository->getPaginatedUsersCriteria(
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

        $this->assertEquals(4, $result->total());
        $this->assertEquals(2, $result->count());
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(2, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }
}

<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindLanByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    public function setUp()
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testFindLanById()
    {
        $lan = factory('App\Model\Lan')->create();
        $foundLan = $this->lanRepository->findLanById($lan->id);

        $this->assertEquals($lan->id, $foundLan->id);
    }
}

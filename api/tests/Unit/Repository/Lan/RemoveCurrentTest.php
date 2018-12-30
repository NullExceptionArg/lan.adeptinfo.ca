<?php

namespace Tests\Unit\Repository\Lan;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class RemoveCurrentTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');
    }

    public function testRemoveCurrent(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);
        factory('App\Model\Lan')->create();

        $this->seeInDatabase('lan', [
            'id' => $lan->id,
            'is_current' => true
        ]);

        $this->lanRepository->removeCurrent();

        $this->seeInDatabase('lan', [
            'id' => $lan->id,
            'is_current' => false
        ]);
    }
}

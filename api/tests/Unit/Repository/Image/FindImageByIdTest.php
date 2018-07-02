<?php

namespace Tests\Unit\Repository\Image;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class FindImageByIdTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\ImageRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testFindImageById(): void
    {
        $image = $this->lanRepository->findImageById($this->image->id);

        $this->assertEquals($this->image->id, $image->id);
        $this->assertEquals($this->image->image, $image->image);
        $this->assertEquals($this->image->lan_id, $image->lan_id);
    }
}

<?php

namespace Tests\Unit\Repository\Image;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateImageForLanTest extends TestCase
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
        $this->image = factory('App\Model\Image')->make([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testCreateImageForLan(): void
    {
        $image = $this->lanRepository->createImageForLan($this->lan->id, $this->image->image);

        $this->assertEquals($this->image->image, $image->image);
        $this->assertEquals($this->image->lan_id, $image->lan_id);

        $this->seeInDatabase('image', [
            'image' => $this->image->image,
            'lan_id' => $this->lan->id
        ]);
    }
}

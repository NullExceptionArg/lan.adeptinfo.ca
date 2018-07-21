<?php

namespace Tests\Unit\Repository\Image;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $imageRepository;

    protected $lan;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->imageRepository = $this->app->make('App\Repositories\Implementation\ImageRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteImage(): void
    {
        $this->seeInDatabase('image', [
            'image' => $this->image->image,
            'lan_id' => $this->image->lan_id
        ]);

        $imageId = $this->imageRepository->deleteImage($this->image);

        $this->assertEquals($this->image->id, $imageId);

        $this->notSeeInDatabase('image', [
            'image' => $this->image->image,
            'lan_id' => $this->image->lan_id
        ]);
    }
}

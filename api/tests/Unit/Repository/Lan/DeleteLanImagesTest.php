<?php

namespace Tests\Unit\Repository\Lan;

use App\Model\LanImage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DeleteLanImagesTest extends TestCase
{
    use DatabaseMigrations;

    protected $lanRepository;

    protected $lan;
    protected $image;
    protected $image2;

    public function setUp(): void
    {
        parent::setUp();
        $this->lanRepository = $this->app->make('App\Repositories\Implementation\LanRepositoryImpl');

        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
        $this->image2 = factory('App\Model\LanImage')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteLanImages(): void
    {
        $this->seeInDatabase('image', [
            'image' => $this->image->image,
            'lan_id' => $this->image->lan_id
        ]);

        $imageIds = [
            $this->image->id,
            $this->image2->id
        ];

        $this->lanRepository->deleteLanImages($imageIds);

        $image = LanImage::withTrashed()->first();
        $this->assertEquals($this->image->id, $image->id);
    }
}

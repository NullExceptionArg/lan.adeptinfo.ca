<?php

namespace Tests\Unit\Service\Image;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class DeleteImagesTest extends TestCase
{
    use DatabaseMigrations;

    protected $imageService;
    protected $user;
    protected $lan;
    protected $image;

    public function setUp(): void
    {
        parent::setUp();
        $this->imageService = $this->app->make('App\Services\Implementation\ImageServiceImpl');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();
        $this->image = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
    }

    public function testDeleteImages(): void
    {
        $image1 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $image2 = factory('App\Model\Image')->create([
            'lan_id' => $this->lan->id
        ]);
        $result = $this->imageService->deleteImages($this->lan->id, $image1->id . ',' . $image2->id);

        $this->assertEquals([
            $image1->id,
            $image2->id
        ], $result);
    }

    public function testDeleteImagesLanIdExists(): void
    {
        $badLanId = -1;
        try {
            $this->imageService->deleteImages($badLanId, $this->image->id);
            $this->fail('Expected: {"lan_id":["The selected lan id is invalid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The selected lan id is invalid."]}', $e->getMessage());
        }
    }

    public function testDeleteImagesLanIdInteger(): void
    {
        $badLanId = '☭';
        try {
            $this->imageService->deleteImages($badLanId, $this->image->id);
            $this->fail('Expected: {"lan_id":["The lan id must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_id":["The lan id must be an integer."]}', $e->getMessage());
        }
    }

    public function testDeleteImagesImagesIdString(): void
    {
        $badImageId = -1;
        try {
            $this->imageService->deleteImages($this->lan->id, $badImageId);
            $this->fail('Expected: {"images_id":["Images with id ' . $badImageId . ' don\'t exist."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"images_id":["Images with id ' . $badImageId . ' don\'t exist."]}', $e->getMessage());
        }
    }
}

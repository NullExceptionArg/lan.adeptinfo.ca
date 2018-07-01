<?php

namespace Tests\Unit\Controller\Image;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class addImageTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'memes' => 'memes'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

//        Storage::fake('tests');
//        $this->requestContent['image'] = UploadedFile::fake()->image('test.jpg');
    }

    public function testAddImage(): void
    {
        $path = base_path() . '/resources/misc/misc.png';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $this->lan->id . '/image', $this->requestContent)
            ->seeJsonEquals([
                'id' => 1,
                'image' => $base64,
                'lan_id' => $this->lan->id
            ])
            ->assertResponseStatus(201);
    }

    public function testAddImageLanIdExists(): void
    {

        $badLanId = -1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan/' . $badLanId . '/image', [
                'image' => UploadedFile::fake()->image('test.jpg')
            ])
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_id' => [
                        0 => 'The selected lan id is invalid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

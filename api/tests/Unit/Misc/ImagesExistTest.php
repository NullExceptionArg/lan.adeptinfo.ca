<?php

namespace Tests\Unit\Misc;

use GuzzleHttp\Client;
use Tests\TestCase;

class ImagesExistTest extends TestCase
{
    public function testBanner(): void
    {
        $client = new Client(['timeout' => 10.0]);
        $image = $client->get(env('BANNER_URL'))->getBody();
        $this->assertEquals(file_get_contents(base_path() . '/resources/views/emails/assets/banner.jpg'), $image->getContents());
    }
}

<?php

namespace Tests\Unit\Misc;

use GuzzleHttp\Client;
use Tests\TestCase;

class ImagesExistTest extends TestCase
{
    public function testBannerImageExist(): void
    {
        $client = new Client(['base_uri' => env('BANNER_URL')]);
        $image = $client->request('GET', '')->getBody();
        $this->assertEquals(file_get_contents(base_path() . '/resources/views/emails/assets/banner.jpg'), $image->getContents());
    }

    public function testFooterImageExist(): void
    {
        $client = new Client(['base_uri' => env('FOOTER_URL')]);
        $image = $client->request('GET', '')->getBody();
        $this->assertEquals(file_get_contents(base_path() . '/resources/views/emails/assets/footer.jpg'), $image->getContents());
    }
}

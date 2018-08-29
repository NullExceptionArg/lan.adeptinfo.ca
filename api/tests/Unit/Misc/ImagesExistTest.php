<?php

namespace Tests\Unit\Misc;

use GuzzleHttp\Client;
use Tests\TestCase;

class ImagesExistTest extends TestCase
{
    public function testBannerImageExist(): void
    {
        $client = new Client(['base_uri' => 'https://raw.githubusercontent.com']);
        $image = $client->request('GET', 'ADEPT-Informatique/lan.adeptinfo.ca/feature/email-confirmation/api/resources/views/emails/assets/banner.jpg')->getBody();
        $this->assertEquals(file_get_contents(base_path() . '/resources/views/emails/assets/banner.jpg'), $image->getContents());
    }

    public function testFooterImageExist(): void
    {
        $url = parse_url(env('FOOTER_URL'));
        $client = new Client(['base_uri' => $url['scheme'] . '://' . $url['host']]);
        $image = $client->request('GET', ltrim($url['path'], '/'))->getBody();
        $this->assertEquals(file_get_contents(base_path() . '/resources/views/emails/assets/footer.jpg'), $image->getContents());
    }
}

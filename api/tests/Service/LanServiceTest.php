<?php


use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;

class LanServiceTest extends TestCase
{
    protected $lanService;

    use DatabaseMigrations;

    protected $paramsContent = [
        'lan_start' => "2100-10-11T12:00:00",
        'lan_end' => "2100-10-12T12:00:00",
        'reservation_start' => "2100-10-04T12:00:00",
        'tournament_start' => "2100-10-07T00:00:00",
        "event_key_id" => "123456789",
        "public_key_id" => "123456789",
        "secret_key_id" => "123456789",
        "price" => 0
    ];

    public function setUp()
    {
        parent::setUp();
        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testCreateLan()
    {
        // Default
        $request = new Request($this->paramsContent);
        $result = $this->lanService->createLan($request);

        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['reservation_start'], $result->reservation_start);
        $this->assertEquals($this->paramsContent['tournament_start'], $result->tournament_start);
        $this->assertEquals($this->paramsContent['event_key_id'], $result->event_key_id);
        $this->assertEquals($this->paramsContent['public_key_id'], $result->public_key_id);
        $this->assertEquals($this->paramsContent['secret_key_id'], $result->secret_key_id);
        $this->assertEquals($this->paramsContent['price'], $result->price);
    }
}

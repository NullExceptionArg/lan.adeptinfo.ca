<?php

namespace Tests\Unit\Service\Lan;

use DateInterval;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class CreateLanTest extends TestCase
{
    protected $lanService;

    use DatabaseMigrations;

    protected $paramsContent = [
        'lan_start' => "2100-10-11T12:00:00",
        'lan_end' => "2100-10-12T12:00:00",
        'seat_reservation_start' => "2100-10-04T12:00:00",
        'tournament_reservation_start' => "2100-10-07T00:00:00",
        "event_key_id" => "",
        "public_key_id" => "",
        "secret_key_id" => "",
        "price" => 0,
        "rules" => '☭'
    ];

    public function setUp()
    {
        parent::setUp();

        $this->paramsContent['event_key_id'] = env('EVENT_KEY_ID');
        $this->paramsContent['secret_key_id'] = env('SECRET_KEY_ID');
        $this->paramsContent['public_key_id'] = env('PUBLIC_KEY_ID');

        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testCreateLan()
    {
        $request = new Request($this->paramsContent);
        $result = $this->lanService->createLan($request);

        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['seat_reservation_start'], $result->seat_reservation_start);
        $this->assertEquals($this->paramsContent['tournament_reservation_start'], $result->tournament_reservation_start);
        $this->assertEquals($this->paramsContent['event_key_id'], $result->event_key_id);
        $this->assertEquals($this->paramsContent['public_key_id'], $result->public_key_id);
        $this->assertEquals($this->paramsContent['secret_key_id'], $result->secret_key_id);
        $this->assertEquals($this->paramsContent['price'], $result->price);
    }

    public function testCreateLanPriceDefault()
    {
        $this->paramsContent['price'] = '';
        $request = new Request($this->paramsContent);
        $result = $this->lanService->createLan($request);

        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['seat_reservation_start'], $result->seat_reservation_start);
        $this->assertEquals($this->paramsContent['tournament_reservation_start'], $result->tournament_reservation_start);
        $this->assertEquals($this->paramsContent['event_key_id'], $result->event_key_id);
        $this->assertEquals($this->paramsContent['public_key_id'], $result->public_key_id);
        $this->assertEquals($this->paramsContent['secret_key_id'], $result->secret_key_id);
        $this->assertEquals(0, $result->price);
    }

    /**
     * @throws Exception
     */
    public function testCreateLanStartRequired()
    {
        $this->paramsContent['lan_start'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_start":["The lan start field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start field is required."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanStartAfterReservationStart()
    {
        // Set the lan_start date to one day before reservation
        $newLanStart = (new DateTime($this->paramsContent['seat_reservation_start']));
        $newLanStart->sub(new DateInterval('P1D'));
        $this->paramsContent['lan_start'] = $newLanStart->format('Y-m-d\TH:i:s');
        // Set the tournament_reservation_start to one day before the new lan_start
        $newTournamentStart = (new DateTime($this->paramsContent['lan_start']));
        $newTournamentStart->sub(new DateInterval('P1D'));
        $this->paramsContent['tournament_reservation_start'] = $newTournamentStart->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_start":["The lan start must be a date after seat reservation start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after seat reservation start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanStartAfterTournamentStart()
    {
        // Set the lan_start date to one day before tournament start
        $newLanStart = (new DateTime($this->paramsContent['tournament_reservation_start']));
        $newLanStart->sub(new DateInterval('P1D'));
        $this->paramsContent['lan_start'] = $newLanStart->format('Y-m-d\TH:i:s');
        // Set the reservation_start to one day before the new lan_start
        $newTournamentStart = (new DateTime($this->paramsContent['lan_start']));
        $newTournamentStart->sub(new DateInterval('P1D'));
        $this->paramsContent['seat_reservation_start'] = $newTournamentStart->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_start":["The lan start must be a date after tournament start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after tournament reservation start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanEndRequired()
    {
        $this->paramsContent['lan_end'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_end":["The lan end field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_end":["The lan end field is required."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanEndAfterLanStart()
    {
        // Set the lan_end date to one day before lan_start
        $newLanEnd = (new DateTime($this->paramsContent['lan_start']));
        $newLanEnd->sub(new DateInterval('P1D'));
        $this->paramsContent['lan_end'] = $newLanEnd->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_end":["The lan end must be a date after lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_end":["The lan end must be a date after lan start."]}', $e->getMessage());
        }
    }

    public function testCreateLanReservationStartRequired()
    {
        $this->paramsContent['seat_reservation_start'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"seat_reservation_start":["The seat reservation start field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_reservation_start":["The seat reservation start field is required."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanReservationStartAfterOrEqualNow()
    {
        // Set the reservation_start date to one day before today
        $newTournamentStart = (new DateTime());
        $newTournamentStart->sub(new DateInterval('P1D'));
        $this->paramsContent['seat_reservation_start'] = $newTournamentStart->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"seat_reservation_start":["The seat reservation start must be a date after or equal to now."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"seat_reservation_start":["The seat reservation start must be a date after or equal to now."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanTournamentStartRequired()
    {
        $this->paramsContent['tournament_reservation_start'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"tournament_reservation_start":["The tournament reservation start field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_reservation_start":["The tournament reservation start field is required."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanTournamentStartAfterOrEqualNow()
    {
        // Set the tournament_reservation_start date to one day before today
        $newTournamentReservationStart = (new DateTime());
        $newTournamentReservationStart->sub(new DateInterval('P1D'));
        $this->paramsContent['tournament_reservation_start'] = $newTournamentReservationStart->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"tournament_reservation_start":["The tournament reservation start must be a date after or equal to now."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"tournament_reservation_start":["The tournament reservation start must be a date after or equal to now."]}', $e->getMessage());
        }
    }

    public function testCreateLanEventKeyIdRequired()
    {
        $this->paramsContent['event_key_id'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanEventKeyIdMaxLength()
    {
        $this->paramsContent['event_key_id'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanPublicKeyIdRequired()
    {
        $this->paramsContent['public_key_id'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"public_key_id":["The public key id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"public_key_id":["The public key id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanPublicKeyIdMaxLength()
    {
        $this->paramsContent['public_key_id'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"public_key_id":["The public key id may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"public_key_id":["The public key id may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKeyIdRequired()
    {
        $this->paramsContent['secret_key_id'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"secret_key_id":["The secret key id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"secret_key_id":["The secret key id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKeyIdMaxLength()
    {
        $this->paramsContent['secret_key_id'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"secret_key_id":["The secret key id may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"secret_key_id":["The secret key id may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanMinimum()
    {
        $this->paramsContent['price'] = "-1";
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"price":["The price must be at least 0."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be at least 0."]}', $e->getMessage());
        }
    }

    public function testCreateLanInteger()
    {
        $this->paramsContent['price'] = "☭";
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"price":["The price must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"price":["The price must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKeyId()
    {
        $this->paramsContent['secret_key_id'] = '-1';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"secret_key_id":["Secret key id: ' . $this->paramsContent['secret_key_id'] . ' is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"secret_key_id":["Secret key id: ' . $this->paramsContent['secret_key_id'] . ' is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanEventKeyId()
    {
        $this->paramsContent['event_key_id'] = '-1';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["Event key id: ' . $this->paramsContent['event_key_id'] . ' is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["Event key id: ' . $this->paramsContent['event_key_id'] . ' is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanRulesString()
    {
        $this->paramsContent['rules'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"rules":["The rules must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"rules":["The rules must be a string."]}', $e->getMessage());
        }
    }
}

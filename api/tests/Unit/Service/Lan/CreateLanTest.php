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
        'name' => "Bolshevik Revolution",
        'lan_start' => "2100-10-11 12:00:00",
        'lan_end' => "2100-10-12 12:00:00",
        'seat_reservation_start' => "2100-10-04 12:00:00",
        'tournament_reservation_start' => "2100-10-07 00:00:00",
        "event_key_id" => "",
        "public_key" => "",
        "secret_key" => "",
        "latitude" => -67.5,
        "longitude" => 64.033333,
        "places" => 10,
        "price" => 0,
        "rules" => '☭',
        "description" => '☭'
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->paramsContent['event_key_id'] = env('EVENT_KEY');
        $this->paramsContent['secret_key'] = env('SECRET_KEY');
        $this->paramsContent['public_key'] = env('PUBLIC_KEY');

        $this->lanService = $this->app->make('App\Services\Implementation\LanServiceImpl');
    }

    public function testCreateLan(): void
    {
        $request = new Request($this->paramsContent);
        $result = $this->lanService->createLan($request);

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['seat_reservation_start'], $result->seat_reservation_start);
        $this->assertEquals($this->paramsContent['tournament_reservation_start'], $result->tournament_reservation_start);
        $this->assertEquals($this->paramsContent['event_key_id'], $result->event_key_id);
        $this->assertEquals($this->paramsContent['public_key'], $result->public_key);
        $this->assertEquals($this->paramsContent['secret_key'], $result->secret_key);
        $this->assertEquals($this->paramsContent['latitude'], $result->latitude);
        $this->assertEquals($this->paramsContent['longitude'], $result->longitude);
        $this->assertEquals($this->paramsContent['places'], $result->places);
        $this->assertEquals($this->paramsContent['price'], $result->price);
        $this->assertEquals($this->paramsContent['rules'], $result->rules);
        $this->assertEquals($this->paramsContent['description'], $result->description);
    }

    public function testCreateLanPriceDefault(): void
    {
        $this->paramsContent['price'] = '';
        $request = new Request($this->paramsContent);
        $result = $this->lanService->createLan($request);

        $this->assertEquals($this->paramsContent['name'], $result->name);
        $this->assertEquals($this->paramsContent['lan_start'], $result->lan_start);
        $this->assertEquals($this->paramsContent['lan_end'], $result->lan_end);
        $this->assertEquals($this->paramsContent['seat_reservation_start'], $result->seat_reservation_start);
        $this->assertEquals($this->paramsContent['tournament_reservation_start'], $result->tournament_reservation_start);
        $this->assertEquals($this->paramsContent['event_key_id'], $result->event_key_id);
        $this->assertEquals($this->paramsContent['public_key'], $result->public_key);
        $this->assertEquals($this->paramsContent['secret_key'], $result->secret_key);
        $this->assertEquals($this->paramsContent['latitude'], $result->latitude);
        $this->assertEquals($this->paramsContent['longitude'], $result->longitude);
        $this->assertEquals(0, $result->price);
        $this->assertEquals($this->paramsContent['rules'], $result->rules);
        $this->assertEquals($this->paramsContent['description'], $result->description);
    }

    public function testCreateLanNameRequired(): void
    {
        $this->paramsContent['name'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"name":["The name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanNameMaxLength(): void
    {
        $this->paramsContent['name'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"name":["The name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"name":["The name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanStartRequired(): void
    {
        $this->paramsContent['lan_start'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"lan_start":["The lan start field is required."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start field is required."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanStartAfterReservationStart(): void
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
            $this->fail('Expected: {"lan_start":["The lan start must be a date after seat reservation start."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after seat reservation start."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanStartAfterTournamentStart(): void
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
            $this->fail('Expected: {"lan_start":["The lan start must be a date after tournament reservation start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after tournament reservation start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanEndRequired(): void
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
    public function testCreateLanEndAfterLanStart(): void
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

    public function testCreateLanSeatReservationStartRequired(): void
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
    public function testCreateLanSeatReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanSeatReservation = (new DateTime($this->paramsContent['lan_start']));
        $newLanSeatReservation->add(new DateInterval('P1D'));
        $this->paramsContent['seat_reservation_start'] = $newLanSeatReservation->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected:{"lan_start":["The lan start must be a date after seat reservation start."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after seat reservation start."],"seat_reservation_start":["The seat reservation start must be a date before or equal to lan start."]}', $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function testCreateLanTournamentStartRequired(): void
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
    public function testCreateLanTournamentReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanTournamentReservation = (new DateTime($this->paramsContent['lan_start']));
        $newLanTournamentReservation->add(new DateInterval('P1D'));
        $this->paramsContent['tournament_reservation_start'] = $newLanTournamentReservation->format('Y-m-d\TH:i:s');
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected:{"lan_start":["The lan start must be a date after tournament reservation start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"lan_start":["The lan start must be a date after tournament reservation start."],"tournament_reservation_start":["The tournament reservation start must be a date before or equal to lan start."]}', $e->getMessage());
        }
    }

    public function testCreateLanEventKeyRequired(): void
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

    public function testCreateLanEventKeyMaxLength(): void
    {
        $this->paramsContent['event_key_id'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id may not be greater than 255 characters.","The event key id is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id may not be greater than 255 characters.","The event key id is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanPublicKeyRequired(): void
    {
        $this->paramsContent['public_key'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"public_key":["The public key id field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"public_key":["The public key id field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanPublicKeyMaxLength(): void
    {
        $this->paramsContent['public_key'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"public_key":["The public key id may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"public_key":["The public key id may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKeyRequired(): void
    {
        $this->paramsContent['secret_key'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id is not valid."],"secret_key":["The secret key field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id is not valid."],"secret_key":["The secret key field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKeyMaxLength(): void
    {
        $this->paramsContent['secret_key'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id is not valid."],"secret_key":["The secret key may not be greater than 255 characters.","The secret key secret key is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id is not valid."],"secret_key":["The secret key may not be greater than 255 characters.","The secret key secret key is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanSecretKey(): void
    {
        $this->paramsContent['secret_key'] = '-1';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id is not valid."],"secret_key":["The secret key secret key is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id is not valid."],"secret_key":["The secret key secret key is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanEventKey(): void
    {
        $this->paramsContent['event_key_id'] = '-1';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"event_key_id":["The event key id is not valid."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"event_key_id":["The event key id is not valid."]}', $e->getMessage());
        }
    }

    public function testCreateLanLatitudeRequired(): void
    {
        $this->paramsContent['latitude'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"latitude":["The latitude field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"latitude":["The latitude field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanLatitudeMin(): void
    {
        $this->paramsContent['latitude'] = -86;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"latitude":["The latitude must be at least -85."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"latitude":["The latitude must be at least -85."]}', $e->getMessage());
        }
    }

    public function testCreateLanLatitudeMax(): void
    {
        $this->paramsContent['latitude'] = 86;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"latitude":["The latitude may not be greater than 85."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"latitude":["The latitude may not be greater than 85."]}', $e->getMessage());
        }
    }

    public function testCreateLanLatitudeNumeric(): void
    {
        $this->paramsContent['latitude'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"latitude":["The latitude must be a number."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"latitude":["The latitude must be a number."]}', $e->getMessage());
        }
    }

    public function testCreateLanLongitudeRequired(): void
    {
        $this->paramsContent['longitude'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"longitude":["The longitude field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"longitude":["The longitude field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanLongitudeMin(): void
    {
        $this->paramsContent['longitude'] = -186;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"longitude":["The longitude must be at least -180."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"longitude":["The longitude must be at least -180."]}', $e->getMessage());
        }
    }

    public function testCreateLanLongitudeMax(): void
    {
        $this->paramsContent['longitude'] = 186;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"longitude":["The longitude may not be greater than 180."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"longitude":["The longitude may not be greater than 180."]}', $e->getMessage());
        }
    }

    public function testCreateLanLongitudeNumeric(): void
    {
        $this->paramsContent['longitude'] = '☭';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"longitude":["The longitude must be a number."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"longitude":["The longitude must be a number."]}', $e->getMessage());
        }
    }

    public function testCreateLanPriceMinimum(): void
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

    public function testCreateLanPriceInteger(): void
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

    public function testCreateLanPlacesRequired(): void
    {
        $this->paramsContent['places'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"places":["The places field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"places":["The places field is required."]}', $e->getMessage());
        }
    }

    public function testCreateLanPlacesMin(): void
    {
        $this->paramsContent['places'] = 0;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"places":["The places must be at least 1."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"places":["The places must be at least 1."]}', $e->getMessage());
        }
    }

    public function testCreateLanPlacesInt(): void
    {
        $this->paramsContent['places'] = "☭";
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"places":["The places must be an integer."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"places":["The places must be an integer."]}', $e->getMessage());
        }
    }

    public function testCreateLanRulesString(): void
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

    public function testCreateLanDescriptionString()
    {
        $this->paramsContent['description'] = 1;
        $request = new Request($this->paramsContent);
        try {
            $this->lanService->createLan($request);
            $this->fail('Expected: {"description":["The description must be a string."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"description":["The description must be a string."]}', $e->getMessage());
        }
    }
}

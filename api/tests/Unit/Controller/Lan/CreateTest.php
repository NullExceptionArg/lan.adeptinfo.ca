<?php

namespace Tests\Unit\Controller\Lan;

use Carbon\Carbon;
use DateInterval;
use Exception;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseMigrations;

    protected $user;

    protected $requestContent = [
        'name' => "Bolshevik Revolution",
        'lan_start' => "2100-10-11 12:00:00",
        'lan_end' => "2100-10-12 12:00:00",
        'seat_reservation_start' => "2100-10-04 12:00:00",
        'tournament_reservation_start' => "2100-10-07 00:00:00",
        "event_key" => "",
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

        $this->requestContent['event_key'] = env('EVENT_TEST_KEY');
        $this->requestContent['secret_key'] = env('SECRET_TEST_KEY');
        $this->requestContent['public_key'] = env('SECRET_TEST_KEY');

        $this->user = factory('App\Model\User')->create();

        $this->addGlobalPermissionToUser(
            $this->user->id,
            'create-lan'
        );
    }


    public function testCreate(): void
    {
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'name' => $this->requestContent['name'],
                'lan_start' => $this->requestContent['lan_start'],
                'lan_end' => $this->requestContent['lan_end'],
                'seat_reservation_start' => $this->requestContent['seat_reservation_start'],
                'tournament_reservation_start' => $this->requestContent['tournament_reservation_start'],
                "event_key" => $this->requestContent['event_key'],
                "public_key" => $this->requestContent['public_key'],
                "secret_key" => $this->requestContent['secret_key'],
                "latitude" => $this->requestContent['latitude'],
                "longitude" => $this->requestContent['longitude'],
                "places" => $this->requestContent['places'],
                "price" => $this->requestContent['price'],
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                'is_current' => true,
                "id" => 1
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testCreateHasCurrentLan(): void
    {
        $this->actingAs($this->user)
            ->call('POST', '/api/lan', $this->requestContent);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'name' => $this->requestContent['name'],
                'lan_start' => $this->requestContent['lan_start'],
                'lan_end' => $this->requestContent['lan_end'],
                'seat_reservation_start' => $this->requestContent['seat_reservation_start'],
                'tournament_reservation_start' => $this->requestContent['tournament_reservation_start'],
                "event_key" => $this->requestContent['event_key'],
                "public_key" => $this->requestContent['public_key'],
                "secret_key" => $this->requestContent['secret_key'],
                "latitude" => $this->requestContent['latitude'],
                "longitude" => $this->requestContent['longitude'],
                "places" => $this->requestContent['places'],
                "price" => $this->requestContent['price'],
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                'is_current' => false,
                "id" => 2
            ])
            ->assertResponseStatus(201);
    }

    public function testCreatePriceDefault(): void
    {
        $this->requestContent['price'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'name' => $this->requestContent['name'],
                'lan_start' => $this->requestContent['lan_start'],
                'lan_end' => $this->requestContent['lan_end'],
                'seat_reservation_start' => $this->requestContent['seat_reservation_start'],
                'tournament_reservation_start' => $this->requestContent['tournament_reservation_start'],
                "event_key" => $this->requestContent['event_key'],
                "public_key" => $this->requestContent['public_key'],
                "secret_key" => $this->requestContent['secret_key'],
                "places" => $this->requestContent['places'],
                'is_current' => true,
                "latitude" => $this->requestContent['latitude'],
                "longitude" => $this->requestContent['longitude'],
                "price" => 0,
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                "id" => 1
            ])
            ->assertResponseStatus(201);
    }

    public function testCreateNameRequired(): void
    {
        $this->requestContent['name'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name must be a string.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'name' => [
                        0 => 'The name may not be greater than 255 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateStartRequired(): void
    {
        $this->requestContent['lan_start'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_start' => [
                        0 => 'The lan start field is required.',
                    ],
                    'seat_reservation_start' => [
                        'The seat reservation start must be a date before or equal to lan start.'
                    ],
                    'tournament_reservation_start' => [
                        'The tournament reservation start must be a date before or equal to lan start.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws Exception
     */
    public function testCreateAfterReservation(): void
    {
        // Set the lan_start date to one day before reservation
        $newLanStart = Carbon::parse($this->requestContent['seat_reservation_start']);
        $newLanStart->sub(new DateInterval('P1D'));
        $this->requestContent['lan_start'] = $newLanStart->format('Y-m-d\TH:i:s');
        // Set the tournament_reservation_start to one day before the new lan_start
        $newTournamentStart = Carbon::parse($this->requestContent['lan_start']);
        $newTournamentStart->sub(new DateInterval('P1D'));
        $this->requestContent['tournament_reservation_start'] = $newTournamentStart->format('Y-m-d\TH:i:s');
        // Execute request
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_start' => [
                        0 => 'The lan start must be a date after seat reservation start.',
                    ],
                    'seat_reservation_start' => [
                        'The seat reservation start must be a date before or equal to lan start.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws Exception
     */
    public function testCreateAfterTournamentStart(): void
    {
        // Set the lan_start date to one day before tournament start
        $newLanStart = Carbon::parse($this->requestContent['tournament_reservation_start']);
        $newLanStart->sub(new DateInterval('P1D'));
        $this->requestContent['lan_start'] = $newLanStart->format('Y-m-d\TH:i:s');
        // Set the seat_reservation_start to one day before the new lan_start
        $newTournamentStart = Carbon::parse($this->requestContent['lan_start']);
        $newTournamentStart->sub(new DateInterval('P1D'));
        $this->requestContent['seat_reservation_start'] = $newTournamentStart->format('Y-m-d\TH:i:s');
        // Execute request
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_start' => [
                        0 => 'The lan start must be a date after tournament reservation start.',
                    ],
                    'tournament_reservation_start' => [
                        'The tournament reservation start must be a date before or equal to lan start.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws \Exception
     */
    public function testCreateEndRequired(): void
    {
        $this->requestContent['lan_end'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_end' => [
                        0 => 'The lan end field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws Exception
     */
    public function testCreateEndAfterLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanEnd = Carbon::parse($this->requestContent['lan_start']);
        $newLanEnd->sub(new DateInterval('P1D'));
        $this->requestContent['lan_end'] = $newLanEnd->format('Y-m-d\TH:i:s');
        // Execute request
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_end' => [
                        0 => 'The lan end must be a date after lan start.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateSeatReservationStartRequired(): void
    {
        $this->requestContent['seat_reservation_start'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'seat_reservation_start' => [
                        0 => 'The seat reservation start field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws Exception
     */
    public function testCreateSeatReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanSeatReservation = Carbon::parse($this->requestContent['lan_start']);
        $newLanSeatReservation->add(new DateInterval('P1D'));
        $this->requestContent['seat_reservation_start'] = $newLanSeatReservation->format('Y-m-d\TH:i:s');
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_start' => [
                        'The lan start must be a date after seat reservation start.'
                    ],
                    'seat_reservation_start' => [
                        'The seat reservation start must be a date before or equal to lan start.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws \Exception
     */
    public function testCreateTournamentStartRequired(): void
    {
        $this->requestContent['tournament_reservation_start'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'tournament_reservation_start' => [
                        0 => 'The tournament reservation start field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    /**
     * @throws Exception
     */
    public function testCreateTournamentReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanTournamentReservation = Carbon::parse($this->requestContent['lan_start']);
        $newLanTournamentReservation->add(new DateInterval('P1D'));
        $this->requestContent['tournament_reservation_start'] = $newLanTournamentReservation->format('Y-m-d\TH:i:s');
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'lan_start' => [
                        'The lan start must be a date after tournament reservation start.'
                    ],
                    'tournament_reservation_start' => [
                        'The tournament reservation start must be a date before or equal to lan start.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateEventKeyRequired(): void
    {
        $this->requestContent['event_key'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'event_key' => [
                        0 => 'The event key field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateEventKeyMaxLength(): void
    {
        $this->requestContent['event_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'event_key' => [
                        0 => 'The event key may not be greater than 255 characters.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePublicKeyRequired(): void
    {
        $this->requestContent['public_key'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'public_key' => [
                        0 => 'The public key field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePublicKeyMaxLength(): void
    {
        $this->requestContent['public_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'public_key' => [
                        0 => 'The public key may not be greater than 255 characters.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateSecretKeyRequired(): void
    {
        $this->requestContent['secret_key'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'secret_key' => [
                        0 => 'The secret key field is required.',
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateSecretKeyMaxLength(): void
    {
        $this->requestContent['secret_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'secret_key' => [
                        0 => 'The secret key may not be greater than 255 characters.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLatitudeRequired(): void
    {
        $this->requestContent['latitude'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'latitude' => [
                        0 => 'The latitude field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLatitudeMin(): void
    {
        $this->requestContent['latitude'] = -86;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'latitude' => [
                        0 => 'The latitude must be at least -85.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLatitudeMax(): void
    {
        $this->requestContent['latitude'] = 86;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'latitude' => [
                        0 => 'The latitude may not be greater than 85.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLatitudeNumeric(): void
    {
        $this->requestContent['latitude'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'latitude' => [
                        0 => 'The latitude must be a number.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLongitudeRequired(): void
    {
        $this->requestContent['longitude'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'longitude' => [
                        0 => 'The longitude field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLongitudeMin(): void
    {
        $this->requestContent['longitude'] = -181;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'longitude' => [
                        0 => 'The longitude must be at least -180.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLongitudeMax(): void
    {
        $this->requestContent['longitude'] = 181;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'longitude' => [
                        0 => 'The longitude may not be greater than 180.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateLongitudeNumeric(): void
    {
        $this->requestContent['longitude'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'longitude' => [
                        0 => 'The longitude must be a number.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePriceMinimum()
    {
        $this->requestContent['price'] = '-1';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'price' => [
                        0 => 'The price must be at least 0.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'price' => [
                        0 => 'The price must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateSecretKey(): void
    {
        $this->requestContent['secret_key'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'secret_key' => [
                        0 => 'The secret key is not valid.',
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateEventKey(): void
    {
        $this->requestContent['event_key'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'event_key' => [
                        0 => 'The event key is not valid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePlacesRequired(): void
    {
        $this->requestContent['places'] = '';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'places' => [
                        0 => 'The places field is required.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePlacesMin(): void
    {
        $this->requestContent['places'] = 0;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'places' => [
                        0 => 'The places must be at least 1.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreatePlacesInt(): void
    {
        $this->requestContent['places'] = '☭';
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'places' => [
                        0 => 'The places must be an integer.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'rules' => [
                        0 => 'The rules must be a string.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testCreateDescriptionString(): void
    {
        $this->requestContent['description'] = 1;
        $this->actingAs($this->user)
            ->json('POST', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'description' => [
                        0 => 'The description must be a string.'
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }
}

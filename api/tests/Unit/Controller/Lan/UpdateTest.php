<?php

namespace Tests\Unit\Controller\Lan;

use App\Model\Permission;
use Carbon\Carbon;
use DateInterval;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UpdateTest extends TestCase
{

    use DatabaseMigrations;

    protected $user;
    protected $lan;

    protected $requestContent = [
        'lan_id' => null,
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
        $this->requestContent['secret_key'] = env('SECRET_KEY');
        $this->requestContent['public_key'] = env('PUBLIC_KEY');

        $this->user = factory('App\Model\User')->create();
        $this->lan = factory('App\Model\Lan')->create();

        $this->addLanPermissionToUser(
            $this->user->id,
            $this->lan->id,
            'edit-lan'
        );

        $this->requestContent['lan_id'] = $this->lan->id;
    }

    public function testUpdate(): void
    {
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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
                "places" => [
                    'reserved' => 0,
                    'total' => $this->requestContent['places']
                ],
                "price" => $this->requestContent['price'],
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                "id" => 1,
                'images' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testUpdateCurrentLan(): void
    {
        $lan = factory('App\Model\Lan')->create([
            'is_current' => true
        ]);

        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lan->id
        ]);
        $permission = Permission::where('name', 'edit-lan')->first();
        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $this->user->id
        ]);

        $this->requestContent['lan_id'] = null;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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
                "places" => [
                    'reserved' => 0,
                    'total' => $this->requestContent['places']
                ],
                "price" => $this->requestContent['price'],
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                "id" => 2,
                'images' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testUpdateHasPermission(): void
    {
        $user = factory('App\Model\User')->create();
        $this->actingAs($user)
            ->json('PUT', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 403,
                'message' => 'REEEEEEEEEE'
            ])
            ->assertResponseStatus(403);
    }

    public function testUpdatePriceDefault(): void
    {
        $this->requestContent['price'] = '';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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
                "latitude" => $this->requestContent['latitude'],
                "longitude" => $this->requestContent['longitude'],
                "places" => [
                    'reserved' => 0,
                    'total' => $this->requestContent['places']
                ],
                "price" => 0,
                "rules" => $this->requestContent['rules'],
                "description" => $this->requestContent['description'],
                "id" => 1,
                'images' => []
            ])
            ->assertResponseStatus(200);
    }

    public function testUpdateNameString(): void
    {
        $this->requestContent['name'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateNameMaxLength(): void
    {
        $this->requestContent['name'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    /**
     * @throws \Exception
     */
    public function testUpdateAfterReservation(): void
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
            ->json('PUT', '/api/lan', $this->requestContent)
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
     * @throws \Exception
     */
    public function testUpdateAfterTournamentStart(): void
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
            ->json('PUT', '/api/lan', $this->requestContent)
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
    public function testUpdateEndAfterLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanEnd = Carbon::parse($this->requestContent['lan_start']);
        $newLanEnd->sub(new DateInterval('P1D'));
        $this->requestContent['lan_end'] = $newLanEnd->format('Y-m-d\TH:i:s');
        // Execute request
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    /**
     * @throws \Exception
     */
    public function testUpdateSeatReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanSeatReservation = Carbon::parse($this->requestContent['lan_start']);
        $newLanSeatReservation->add(new DateInterval('P1D'));
        $this->requestContent['seat_reservation_start'] = $newLanSeatReservation->format('Y-m-d\TH:i:s');
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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
    public function testUpdateTournamentReservationBeforeOrEqualLanStart(): void
    {
        // Set the lan end date to one day before lan start
        $newLanTournamentReservation = Carbon::parse($this->requestContent['lan_start']);
        $newLanTournamentReservation->add(new DateInterval('P1D'));
        $this->requestContent['tournament_reservation_start'] = $newLanTournamentReservation->format('Y-m-d\TH:i:s');
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateEventKeyMaxLength(): void
    {
        $this->requestContent['event_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdatePublicKeyMaxLength(): void
    {
        $this->requestContent['public_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateSecretKeyMaxLength(): void
    {
        $this->requestContent['secret_key'] = str_repeat('☭', 256);
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'secret_key' => [
                        0 => 'The secret key may not be greater than 255 characters.',
                        1 => 'The secret key is not valid.'
                    ]
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateLatitudeMin(): void
    {
        $this->requestContent['latitude'] = -86;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateLatitudeMax(): void
    {
        $this->requestContent['latitude'] = 86;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateLatitudeNumeric(): void
    {
        $this->requestContent['latitude'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateLongitudeMin(): void
    {
        $this->requestContent['longitude'] = -181;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateLongitudeMax(): void
    {
        $this->requestContent['longitude'] = 181;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateLongitudeNumeric(): void
    {
        $this->requestContent['longitude'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdatePriceMinimum()
    {
        $this->requestContent['price'] = '-1';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdatePriceInteger(): void
    {
        $this->requestContent['price'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateSecretKey(): void
    {
        $this->requestContent['secret_key'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
            ->seeJsonEquals([
                'success' => false,
                'status' => 400,
                'message' => [
                    'secret_key' => [
                        0 => 'The secret key is not valid.',
                    ],
                ]
            ])
            ->assertResponseStatus(400);
    }

    public function testUpdateEventKey(): void
    {
        $this->requestContent['event_key'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdatePlacesMin(): void
    {
        $this->requestContent['places'] = 0;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdatePlacesInt(): void
    {
        $this->requestContent['places'] = '☭';
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateRulesString(): void
    {
        $this->requestContent['rules'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

    public function testUpdateDescriptionString(): void
    {
        $this->requestContent['description'] = 1;
        $this->actingAs($this->user)
            ->json('PUT', '/api/lan', $this->requestContent)
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

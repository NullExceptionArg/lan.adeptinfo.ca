<?php

namespace Tests;

use Laravel\Lumen\Application;
use Seatsio\SeatsioClient;

/**
 * Contexte pour les tests qui utilisent l'API seats.io
 *
 * Class SeatsTestCase
 * @package Tests
 */
abstract class SeatsTestCase extends TestCase
{
    /**
     * Créer l'application
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        // Créer un client pour l'API de seats.io
        $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));

        // Rendre le siège de test comme disponible
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));
    }

    public function tearDown(): void
    {
        // Créer un client pour l'API de seats.io
        $seatsClient = new SeatsioClient(env('SEAT_SECRET_KEY'));

        // Rendre le siège de test comme disponible
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));

        parent::tearDown();
    }
}

<?php

namespace Tests;

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
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        // Créer un client pour l'API de seats.io
        $seatsClient = new SeatsioClient(env('SECRET_TEST_KEY'));

        // Rendre le siège de test comme disponible
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));
    }

    public function tearDown()
    {
        // Créer un client pour l'API de seats.io
        $seatsClient = new SeatsioClient(env('SECRET_TEST_KEY'));

        // Rendre le siège de test comme disponible
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));

        parent::tearDown();
    }
}

<?php

namespace Tests;

use Seatsio\SeatsioClient;

abstract class SeatsTestCase extends TestCase
{
    /**
     * Creates the application.
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

        $seatsClient = new SeatsioClient(env('SECRET_KEY'));
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));
    }

    public function tearDown()
    {
        $seatsClient = new SeatsioClient(env('SECRET_KEY'));
        $seatsClient->events->release(env('EVENT_TEST_KEY'), env('SEAT_TEST_ID'));

        parent::tearDown();
    }
}

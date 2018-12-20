<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();

        $this->artisan('lan:permissions');
        $this->artisan('lan:roles');
        $this->artisan('lan:general-admin', [
            'email' => 'karl.marx@unite.org',
            'first-name' => 'karl',
            'last-name' => 'marx',
            'password' => 'Passw0rd!',
        ]);
    }
}

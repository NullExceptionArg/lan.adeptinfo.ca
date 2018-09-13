<?php

namespace Tests;

use App\Console\Commands\GeneratePermissions;
use Illuminate\Support\Facades\DB;
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

        $permissions = new GeneratePermissions();
        DB::table('permission')->insert($permissions->getPermissions());
    }
}

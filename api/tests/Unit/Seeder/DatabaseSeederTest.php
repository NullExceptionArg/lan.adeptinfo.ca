<?php

namespace Tests\Unit\Seeder;

use App\Model\Lan;
use App\Model\Reservation;
use App\Model\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testDatabaseSeederSimple()
    {
        Artisan::call('db:seed');
        $this->assertEquals(1000, User::count());
        $this->assertEquals(5, Lan::count());
        $this->assertEquals(1000, Reservation::count());
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Exécuter la génération de données fictives.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTableSeeder::class);
        $this->call(LanTableSeeder::class);
        $this->call(ReservationTableSeeder::class);
    }
}

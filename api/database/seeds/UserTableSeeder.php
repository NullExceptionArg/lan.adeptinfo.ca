<?php

use Illuminate\Database\Seeder;

/**
 * Créer 1000 utilisateurs.
 *
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
{
    /**
     * Exécuter la génération de données fictives.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Model\User::class, 1000)->create();
    }
}

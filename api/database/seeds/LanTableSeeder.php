<?php

use Illuminate\Database\Seeder;

/**
 * Créer un LAN avec chacun des clé d'événement de test.
 *
 * Class LanTableSeeder
 */
class LanTableSeeder extends Seeder
{
    /**
     * Exécuter la génération de données fictives.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Model\Lan::class)->create([
            'event_key' => env('EVENT_TEST_KEY_MANY_1'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key' => env('EVENT_TEST_KEY_MANY_2'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key' => env('EVENT_TEST_KEY_MANY_3'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key' => env('EVENT_TEST_KEY_MANY_4'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key' => env('EVENT_TEST_KEY_MANY_5'),
        ]);
    }
}

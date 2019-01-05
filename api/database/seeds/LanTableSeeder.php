<?php

use Illuminate\Database\Seeder;

class LanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
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

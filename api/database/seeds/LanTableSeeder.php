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
            'event_key_id' => env('EVENT_KEY_ID_MANY_1'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID_MANY_2'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID_MANY_3'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID_MANY_4'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID_MANY_5'),
        ]);
    }
}

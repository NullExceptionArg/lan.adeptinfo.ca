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
            'event_key_id' => env('EVENT_KEY_ID-MANY-1'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID-MANY-2'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID-MANY-3'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID-MANY-4'),
        ]);
        factory(App\Model\Lan::class)->create([
            'event_key_id' => env('EVENT_KEY_ID-MANY-5'),
        ]);
    }
}

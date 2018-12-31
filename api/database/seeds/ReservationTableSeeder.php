<?php

use App\Model\Lan;
use App\Model\Reservation;
use App\Model\User;
use Database\MiscData;
use Illuminate\Database\Seeder;
use Seatsio\SeatsioClient;

class ReservationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $seatsClient = new SeatsioClient(env('SECRET_KEY'));

        $mscdt = include(base_path() . '/database/seats.php');
        $places = $mscdt->getSeatData();
        $users = User::all();
        $lans = Lan::all();

        // Reset seats.io
        foreach ($lans as $lan) {
            $seatsClient->events->release($lan->event_key, $places);
        }

        // Fill database and seat.io
        $seatIndex = 0;
        $lanIndex = 0;
        foreach ($users as $user) {

            $lan = $lans[$lanIndex++ % count($lans)];
            $place = $places[$seatIndex++ % count($places)];

            // seats.io
            if (rand(0, 9) > 7) { // 20% of users hve arrived to the LAN
                $seatsClient->events->changeObjectStatus($lan->event_key, [$place], 'arrived');
            } else {
                $seatsClient->events->book($lan->event_key, [$place]);
            }

            // Database
            $reservation = new Reservation();
            $reservation->user_id = $user->id;
            $reservation->lan_id = $lan->id;
            $reservation->seat_id = $place;
            $reservation->save();
        }
    }
}

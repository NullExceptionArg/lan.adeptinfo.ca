<?php

use App\Model\{Lan, User};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Seatsio\SeatsioClient;

/**
 * Créer des réservation pour les LANs et les utilisateurs des l'application.
 *
 * Class ReservationTableSeeder
 */
class ReservationTableSeeder extends Seeder
{
    /**
     * Exécuter la génération de données fictives.
     *
     * @return void
     */
    public function run()
    {
        $seatsClient = new SeatsioClient(env('SECRET_TEST_KEY'));

        // Obtenir les places par défaut d'un LAN
        $places = include(base_path() . '/database/seat.php');

        // Obtenir tout les utilisateurs de l'API
        $users = User::all();

        // Obtenir tout les LAN de l'API
        $lans = Lan::all();

        // Pour chaque LAN, réinitialiser les places
        foreach ($lans as $lan) {
            $seatsClient->events->release($lan->event_key, $places);
        }

        // Pour chaque utilisateur
        $seatIndex = 0;
        $lanIndex = 0;
        foreach ($users as $user) {

            // Obtenir un LAN différent à chaque itération
            $lan = $lans[$lanIndex++ % count($lans)];

            // Obtenir une place différente à chaque itération
            $place = $places[$seatIndex++ % count($places)];

            // Environ 20% des utilisateurs sont arrivés au LAN, les autres ont réservés leurs places
            if (rand(0, 9) > 7) {
                $seatsClient->events->changeObjectStatus($lan->event_key, [$place], 'arrived');
            } else {
                $seatsClient->events->book($lan->event_key, [$place]);
            }

            // Créer la réservation dans la base de donnée
            DB::table('reservation')
                ->insert([
                    'user_id' => $user->id,
                    'lan_id' => $lan->id,
                    'seat_id' => $place,
                ]);
        }
    }
}

<?php

namespace App\Model;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property int lan_id
 * @property string name
 * @property DateTime tournament_start
 * @property DateTime tournament_end
 * @property int players_to_reach
 * @property int teams_to_reach
 * @property string state
 * @property string rules
 * @property int|null price
 */
class Tournament extends Model
{
    protected $table = 'tournament';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    public function getCurrentState()
    {
        //_-hidden, -visible, forthcoming, late, -started, behindhand, -finished
        $state = $this->state;
        $now = new DateTime();
        if ($state == 'hidden') {
            return 'hidden'; // caché
        } else if ($state == 'finished') {
            return 'finished'; // terminé
        } else if ($state == 'visible' && $now < $this->tournament_start) {
            return 'fourthcoming'; // à venir
        } else if ($state == 'visible' && $now >= $this->tournament_start) {
            return 'late'; // en retard
        } else if ($state == 'started' && $now < $this->tournament_start) {
            return 'outguessed'; // devancé
        } else if ($state == 'started' && $now >= $this->tournament_start && $now <= $this->tournament_end) {
            return 'running'; // en cours
        } else if ($state == 'started' && $now > $this->tournament_end) {
            return 'behindhand'; // en retard sur l'horaire
        } else {
            return 'unknown'; // inconnue
        }
    }
}
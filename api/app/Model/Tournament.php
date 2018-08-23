<?php

namespace App\Model;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

    protected $table = 'tournament';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'lan_id' => 'integer', 'players_to_reach' => 'integer', 'teams_to_reach' => 'integer', 'price' => 'integer'
    ];

    public function getCurrentState()
    {
        $state = $this->state;
        $now = Carbon::now();
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
            return 'behindhand'; // en retard sur l'horaire (s'éternise)
        } else {
            return 'unknown'; // inconnue
        }
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($tournament) {
            $teams = Team::where('tournament_id', $tournament->id)
                ->get();
            foreach ($teams as $team) {
                TagTeam::where('team_id', $team->id)
                    ->delete();
                Request::where('team_id', $team->id)
                    ->delete();
                $team->delete();
            }
            OrganizerTournament::where('tournament_id', $tournament->id)
                ->delete();
        });
    }
}
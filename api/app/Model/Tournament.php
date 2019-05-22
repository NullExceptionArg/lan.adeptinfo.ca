<?php

namespace App\Model;

use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Tournoi de jeu organisé dans un LAN.
 *
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
     * Les attributs qui doivent être mutés en dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * Champs qui ne sont pas retournés par défaut lorsque l'objet est retourné dans une requête HTTP.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = [
        'lan_id' => 'integer', 'players_to_reach' => 'integer', 'teams_to_reach' => 'integer', 'price' => 'integer',
    ];

    /**
     * État courant du tournoi selon son état et le moment courant.
     *
     * @return string
     */
    public function getCurrentState()
    {
        $state = $this->state;
        $now = Carbon::now();
        if ($state == 'hidden') {
            return TournamentState::HIDDEN;
        } elseif ($state == 'finished') {
            return TournamentState::FINISHED;
        } elseif ($state == 'visible' && $now < $this->tournament_start) {
            return TournamentState::FOURTHCOMING;
        } elseif ($state == 'visible' && $now >= $this->tournament_start) {
            return TournamentState::LATE;
        } elseif ($state == 'started' && $now < $this->tournament_start) {
            return TournamentState::OUTGUESSED;
        } elseif ($state == 'started' && $now >= $this->tournament_start && $now <= $this->tournament_end) {
            return TournamentState::RUNNING;
        } elseif ($state == 'started' && $now > $this->tournament_end) {
            return TournamentState::BEHINDHAND;
        } else {
            return TournamentState::UNKNOWN;
        }
    }

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression du tournoi
        static::deleting(function ($tournament) {
            $teams = Team::where('tournament_id', $tournament->id)
                ->get();
            // Pour chaque équipe inscrite au tournoi
            foreach ($teams as $team) {
                // Supprimer les liens avec les tag de joueurs
                TagTeam::where('team_id', $team->id)
                    ->delete();
                // Supprimer les liens avec les requêtes pour entrer dans l'équipe
                Request::where('team_id', $team->id)
                    ->delete();
                // Supprimer l'équipe
                $team->delete();
            }
            // Supprimer le les liens avec les organisateurs du tournoi
            OrganizerTournament::where('tournament_id', $tournament->id)
                ->delete();
        });
    }
}

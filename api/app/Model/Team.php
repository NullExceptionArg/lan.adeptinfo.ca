<?php

namespace App\Model;

use Illuminate\{Database\Eloquent\Model, Database\Eloquent\SoftDeletes};

/**
 * Équipe de joueurs (tag) qui participent à un tournoi.
 *
 * @property int id
 * @property int tournament_id
 * @property string name
 * @property string tag
 */
class Team extends Model
{
    use SoftDeletes;

    protected $table = 'team';

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
    protected $casts = ['tournament_id' => 'integer'];

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression de l'équipe
        static::deleting(function ($team) {
            // Supprimer les liens avec tag d'utilisateurs
            TagTeam::where('team_id', $team->id)->delete();
            // Supprimer les liens avec requêtes pour entrer dans l'équipe
            Request::where('team_id', $team->id)->delete();
        });
    }
}

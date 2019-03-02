<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre un utilisateur qui organise un tournoi et le tournoi.
 *
 * @property int organizer_id
 * @property int tournament_id
 */
class OrganizerTournament extends Model
{
    protected $table = 'organizer_tournament';

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
        'tournament_id' => 'integer', 'organizer_id' => 'integer'
    ];
}

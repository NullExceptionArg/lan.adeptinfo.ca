<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int organizer_id
 * @property int tournament_id
 */
class OrganizerTournament extends Model
{
    protected $table = 'organizer_tournament';
}
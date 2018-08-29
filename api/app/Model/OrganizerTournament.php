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

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $casts = [
        'tournament_id' => 'integer', 'organizer_id' => 'integer'
    ];

}
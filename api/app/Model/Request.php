<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Requête pour entrer dans une équipe.
 *
 * @property int team_id
 * @property int tag_id
 */
class Request extends Model
{
    protected $table = 'request';

    /**
     * Champs qui ne sont pas retournés par défaut lorsque l'objet est retourné dans une requête HTTP.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at'
    ];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = ['tag_id' => 'integer', 'team_id' => 'integer'];
}

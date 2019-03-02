<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Tag utilisé par un utilisateur.
 * C'est le nom qu'il choisit pour être affiché en tant que membre d'une équipe.
 *
 * @property string name
 * @property int user_id
 * @property mixed id
 */
class Tag extends Model
{
    protected $table = 'tag';

    /**
     * Champs qui ne sont pas retournés par défaut lorsque l'objet est retourné dans une requête HTTP.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'user_id',
    ];
}

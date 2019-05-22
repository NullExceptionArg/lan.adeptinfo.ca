<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Permission qui protègent les chemins HTTP dont l'accès doit être restraint à des administrateurs.
 *
 * Class Permission
 */
class Permission extends Model
{
    protected $table = 'permission';

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Date mise à jour désactivée.
     *
     * @var bool
     */
    public $timestamps = false;
}

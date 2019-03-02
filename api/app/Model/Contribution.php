<?php

namespace App\Model;

use Illuminate\{Database\Eloquent\Model, Database\Eloquent\SoftDeletes};

/**
 * Une contribution est une remarque qui permet de dire qu'un utilisateur a participé à l'organisation d'un LAN.
 *
 * @property int lan_id
 * @property string user_full_name
 * @property int user_id
 * @property int contribution_category_id
 * @property int id
 */
class Contribution extends Model
{
    use SoftDeletes;

    protected $table = 'contribution';

    public $timestamps = false;

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
    protected $hidden = ['user_id', 'lan_id', 'contribution_category_id', 'deleted_at'];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function Lan()
    {
        return $this->belongsTo(User::class);
    }
}

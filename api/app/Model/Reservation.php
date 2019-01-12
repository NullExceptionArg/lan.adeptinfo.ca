<?php

namespace App\Model;

use DateTime;
use Illuminate\{Database\Eloquent\Model, Database\Eloquent\SoftDeletes};

/**
 * Réservation d'une place par un utilisateur et son état
 *
 * @property int user_id
 * @property int lan_id
 * @property string seat_id
 * @property bool has_arrived
 * @property DateTime left_at
 * @property DateTime arrived_at
 */
class Reservation extends Model
{
    use SoftDeletes;

    protected $table = 'reservation';

    /**
     * Les attributs qui doivent être mutés en dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seat_id', 'lan_id'
    ];

    /**
     * Champs qui ne sont pas retournés par défaut lorsque l'objet est retourné dans une requête HTTP.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id', 'created_at', 'updated_at', 'deleted_at', 'arrived_at', 'left_at',
    ];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = ['lan_id' => 'integer'];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}

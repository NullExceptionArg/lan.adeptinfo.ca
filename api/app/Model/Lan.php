<?php

namespace App\Model;

use DateTime;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Entitée centrale de l'application: événement de jeu en réseau.
 *
 * @property int id
 * @property string name
 * @property DateTime lan_start
 * @property DateTime lan_end
 * @property DateTime seat_reservation_start
 * @property DateTime tournament_reservation_start
 * @property string event_key
 * @property int places
 * @property float longitude
 * @property float latitude
 * @property null|int price
 * @property null|string rules
 * @property null|string description
 * @property bool is_current
 */
class Lan extends Model
{
    use SoftDeletes;

    protected $table = 'lan';

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
        'created_at', 'updated_at', 'deleted_at'
    ];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'integer',
        'places' => 'integer',
        'id' => 'integer',
        'is_current' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float'
    ];

    /**
     * Champs assignables par masses
     *
     * @var array
     */
    protected $fillable = [
        'is_current'
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function contribution()
    {
        return $this->hasMany(Contribution::class);
    }

    public function contributionCategory()
    {
        return $this->hasMany(ContributionCategory::class);
    }

    public static function getCurrent(): ?Lan
    {
        return Lan::where('is_current', true)->first();
    }
}

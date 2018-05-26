<?php

namespace App\Model;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string name
 * @property DateTime lan_start
 * @property DateTime lan_end
 * @property DateTime seat_reservation_start
 * @property DateTime tournament_reservation_start
 * @property string event_key_id
 * @property string public_key_id
 * @property string secret_key_id
 * @property float longitude
 * @property float latitude
 * @property null|int price
 * @property null|string rules
 * @property null|string description
 */
class Lan extends Model
{
    protected $table = 'lan';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    protected $casts = ['price' => 'integer'];


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
}

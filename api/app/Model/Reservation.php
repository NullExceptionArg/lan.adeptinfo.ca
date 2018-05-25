<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int user_id
 * @property int lan_id
 * @property string seat_id
 * @property bool has_arrived
 */
class Reservation extends Model
{
    protected $table = 'reservation';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'seat_id', 'lan_id'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id', 'created_at', 'updated_at',
    ];

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

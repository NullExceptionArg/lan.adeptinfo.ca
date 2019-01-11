<?php

namespace App\Model;

use Illuminate\{Auth\Authenticatable,
    Contracts\Auth\Access\Authorizable as AuthorizableContract,
    Contracts\Auth\Authenticatable as AuthenticatableContract,
    Database\Eloquent\Model};
use Laravel\{Lumen\Auth\Authorizable, Passport\HasApiTokens};
use Seatsio\SeatsioClient;

/**
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property string password
 * @property int id
 * @property string facebook_id
 * @property mixed confirmation_code
 * @property bool is_confirmed
 * @property string google_id
 */
class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use HasApiTokens, Authenticatable, Authorizable;

    protected $table = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'id', 'created_at', 'updated_at', 'facebook_id', 'google_id', 'confirmation_code', 'is_confirmed'
    ];

    public function getFullName(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class);
    }

    public function contribution()
    {
        return $this->hasMany(Contribution::class);
    }

    public function lan()
    {
        return $this->hasManyThrough(
            'App\Model\Lan',
            'App\Model\Reservation'
        );
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            $reservations = Reservation::where('user_id', $user->id)->get();
            foreach ($reservations as $reservation) {
                $lan = Lan::find($reservation->lan_id);

                $seatsClient = new SeatsioClient($lan->secret_key);
                $seatsClient->events->release($lan->event_key, $reservation->seat_id);

                $reservation->delete();
            }

            $contributions = $user->Contribution()->get();
            foreach ($contributions as $contribution) {
                $contribution->ContributionCategory()->detach();
                $contribution->delete();
            }
        });
    }
}

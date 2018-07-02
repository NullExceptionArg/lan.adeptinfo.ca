<?php

namespace App\Model;


use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string lan_id
 * @property string image
 */
class Image extends Model
{
    protected $table = 'image';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at',
    ];

    protected $casts = ['id' => 'integer'];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }
}
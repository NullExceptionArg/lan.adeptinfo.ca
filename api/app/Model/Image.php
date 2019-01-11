<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string lan_id
 * @property string image
 */
class Image extends Model
{
    use SoftDeletes;

    protected $table = 'image';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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

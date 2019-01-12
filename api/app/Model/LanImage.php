<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Image utilisée pour présenter un LAN.
 *
 * @property int id
 * @property string lan_id
 * @property string image
 */
class LanImage extends Model
{
    use SoftDeletes;

    protected $table = 'image';

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
        'created_at', 'updated_at',
    ];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer'];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }
}

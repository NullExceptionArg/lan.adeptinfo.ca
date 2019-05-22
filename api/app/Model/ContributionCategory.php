<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Catégorie qui est attribuée à une contribution.
 *
 * @property int id
 * @property string lan_id
 * @property string name
 */
class ContributionCategory extends Model
{
    use SoftDeletes;

    protected $table = 'contribution_category';

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
    protected $hidden = [
        'lan_id', 'deleted_at',
    ];

    public function Lan()
    {
        return $this->belongsTo(Lan::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression de la catégorie de contribution
        static::deleting(function ($contributionCategory) {
            // Supprimer les contributions qui n'ont plus de catégorie de contribution
            $contributions = Contribution::where('contribution_category_id', $contributionCategory)
                ->get();
            foreach ($contributions as $contribution) {
                if ($contribution->ContributionCategory()->count() <= 1) {
                    $contribution->delete();
                }
            }
        });
    }
}

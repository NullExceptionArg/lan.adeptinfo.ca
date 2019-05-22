<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Rôle effectif sur un LAN en particulier.
 *
 * @property int id
 * @property int lan_id
 * @property string name
 * @property string en_display_name
 * @property string en_description
 * @property string fr_display_name
 * @property string fr_description
 */
class LanRole extends Model
{
    protected $table = 'lan_role';

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
    protected $hidden = ['id', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * Champs à transtyper.
     *
     * @var array
     */
    protected $casts = [
        'id'     => 'integer',
        'lan_id' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression du rôle de LAN
        static::deleting(function ($role) {
            // Supprimer les liens avec les permissions
            PermissionLanRole::where('role_id', $role->id)->delete();
            // Supprimer les liens avec les utilisateurs
            LanRoleUser::where('role_id', $role->id)->delete();
        });
    }
}

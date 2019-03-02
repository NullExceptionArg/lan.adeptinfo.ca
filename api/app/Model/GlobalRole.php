<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Rôle effectif sur l'ensemble de l'application.
 *
 * @property int lan_id
 * @property string name
 * @property string en_display_name
 * @property string en_description
 * @property string fr_display_name
 * @property string fr_description
 */
class GlobalRole extends Model
{
    protected $table = 'global_role';

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

    protected static function boot()
    {
        parent::boot();

        // Avant la suppression du rôle global
        static::deleting(function ($role) {
            // Supprimer les liens avec les permissions
            PermissionGlobalRole::where('role_id', $role->id)->delete();
            // Supprimer les liens avec les utilisateurs
            GlobalRoleUser::where('role_id', $role->id)->delete();
        });
    }
}

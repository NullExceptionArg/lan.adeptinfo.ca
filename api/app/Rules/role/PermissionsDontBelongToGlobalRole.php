<?php

namespace App\Rules\Role;

use App\Model\{GlobalRole, PermissionGlobalRole};
use Illuminate\Contracts\Validation\Rule;

/**
 * Des permissions ne sont pas liées à un rôle global.
 *
 * Class PermissionsDontBelongToGlobalRole
 * @package App\Rules\Role
 */
class PermissionsDontBelongToGlobalRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToGlobalRole constructor.
     * @param string|null $roleId Id du rôle global
     */
    public function __construct(?string $roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  array $permissionIds
     * @return bool
     */
    public function passes($attribute, $permissionIds): bool
    {
        $globalRole = null;

        /*
         * Conditions de garde :
         * Les ids de permission ne sont pas nuls
         * Les ids de permissions sont un tableau
         * L'id du rôle global correspond à un rôle global existant
         */
        if (
            is_null($permissionIds) ||
            !is_array($permissionIds) ||
            is_null($globalRole = GlobalRole::find($this->roleId))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Pour chaque id de permission passé
        foreach ($permissionIds as $permissionId) {

            // Chercher lien entre le rôle et l'id de la permission
            $permission = PermissionGlobalRole::where('permission_id', $permissionId)
                ->where('role_id', $globalRole->id)
                ->get()
                ->first();

            // Si un lien est trouvé
            if (!is_null($permission)) {
                // Le test échoue
                return false;
            }
        }

        return true;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.permissions_dont_belong_to_user');
    }
}
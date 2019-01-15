<?php

namespace App\Rules\Role;

use App\Model\{LanRole, PermissionLanRole};
use Illuminate\Contracts\Validation\Rule;

/**
 * Un lien existe entre des permissions et un rôle de LAN
 *
 * Class PermissionsBelongToLanRole
 * @package App\Rules\Role
 */
class PermissionsBelongToLanRole implements Rule
{
    protected $roleId;

    /**
     * PermissionsDontBelongToGlobalRole constructor.
     * @param int $roleId Id du rôle
     */
    public function __construct(int $roleId)
    {
        $this->roleId = $roleId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  array $permissionIds Id des permissions
     * @return bool
     */
    public function passes($attribute, $permissionIds): bool
    {
        /*
         * Conditions de garde :
         * Les permissions ne sont pas nul
         * Les permissions sont un tableau
         * L'id du rôle n'est pas nul
         */
        if (is_null($permissionIds) || !is_array($permissionIds) || is_null($this->roleId)) {
            return true; // Une autre validation devrait échouer
        }

        $lanRole = LanRole::find($this->roleId);

        // Pour chaque id de permission
        foreach ($permissionIds as $permissionId) {

            // Chercher un lien entre la permission et le rôle de LAN
            $permission = PermissionLanRole::where('permission_id', $permissionId)
                ->where('role_id', $lanRole->id)
                ->get()
                ->first();

            // Si aucune permission n'est trouvée, la validation échoue
            if (is_null($permission)) {
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
        return trans('validation.permissions_belong_to_user');
    }
}
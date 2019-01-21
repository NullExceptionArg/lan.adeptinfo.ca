<?php

namespace App\Rules\Team;

use App\Model\{Team, Tournament};
use Illuminate\{Auth\Access\AuthorizationException, Contracts\Validation\Rule, Support\Facades\DB};

/**
 * Un utilisateur possède une permission de LAN pour le LAN d'une équipe.
 *
 * Class HasPermissionInLan
 * @package App\Rules\Team
 */
class HasPermissionInLan implements Rule
{
    protected $teamId;
    protected $userId;

    /**
     * HasPermissionInLan constructor.
     * @param string|null $teamId Id de l'équipe
     * @param string $userId Id de l'utilisateur
     */
    public function __construct(?string $teamId, string $userId)
    {
        $this->teamId = $teamId;
        $this->userId = $userId;
    }

    /**
     * Déterminer si la règle de validation passe.
     *
     * @param  string $attribute
     * @param  mixed $permission Nom unique de la permission
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $permission): bool
    {
        $team = null;
        $tournament = null;

        /*
         * Conditions de garde :
         * Un nom de permission a été fourni
         * Un id d'utilisateur a été fourni
         * L'id d'équipem fourni correspond à une équipe
         * Un tournoi existe pour l'équipe trouvée
         */
        if (
            is_null($permission) ||
            is_null($this->userId) ||
            is_null($team = Team::find($this->teamId)) ||
            is_null($tournament = Tournament::find($team->id))
        ) {
            return true; // Une autre validation devrait échouer
        }

        // Chercher si l'utilisateur possède la permission dans un rôle de LAN dans le LAN du tournoi trouvé plus haut
        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $tournament->lan_id)
            ->where('lan_role_user.user_id', $this->userId)
            ->where('permission.name', $permission)
            ->get();

        // Chercher si l'utilisateur possède la permission dans un rôle global
        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $permission)
            ->get();

        // Fusionner les rôles trouvés
        $hasPermission = $lanPermissions->merge($globalPermissions)->unique()->count() > 0;

        // Si aucune permission n'a été trouvée, l'utilisateur ne devrait jamais avoir essayé d'accéder à la ressource,
        // d'où l'exception, et non la non réussite de la validation
        if (!$hasPermission) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return $hasPermission;
    }

    /**
     * Obtenir le message d'erreur.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('validation.has_permission');
    }
}
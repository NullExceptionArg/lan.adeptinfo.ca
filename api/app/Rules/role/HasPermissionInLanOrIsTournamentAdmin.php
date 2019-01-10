<?php

namespace App\Rules;

use App\Model\Lan;
use App\Model\OrganizerTournament;
use App\Model\Tournament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class HasPermissionInLanOrIsTournamentAdmin implements Rule
{
    protected $userId;
    protected $tournamentId;

    public function __construct(string $userId, ?string $tournamentId)
    {
        $this->userId = $userId;
        $this->tournamentId = $tournamentId;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value permissions name
     * @return bool
     * @throws AuthorizationException
     */
    public function passes($attribute, $value)
    {
        $tournament = null;
        $lan = null;
        if (
            is_null($value) ||
            is_null($this->userId) ||
            is_null($tournament = Tournament::find($this->tournamentId)) ||
            is_null($lan = Lan::find($tournament->lanId))
        ) {
            return true;
        }

        $lanPermissions = DB::table('permission')
            ->join('permission_lan_role', 'permission.id', '=', 'permission_lan_role.permission_id')
            ->join('lan_role', 'permission_lan_role.role_id', '=', 'lan_role.id')
            ->join('lan', 'lan_role.lan_id', '=', 'lan.id')
            ->join('lan_role_user', 'lan_role.id', '=', 'lan_role_user.role_id')
            ->where('lan_role.lan_id', $lan->id)
            ->where('lan_role_user.user_id', $this->userId)
            ->where('permission.name', $value)
            ->get();

        $globalPermissions = DB::table('permission')
            ->join('permission_global_role', 'permission.id', '=', 'permission_global_role.permission_id')
            ->join('global_role', 'permission_global_role.role_id', '=', 'global_role.id')
            ->join('global_role_user', 'global_role.id', '=', 'global_role_user.role_id')
            ->where('global_role_user.user_id', $this->userId)
            ->where('permission.name', $value)
            ->get();

        $hasPermission = $lanPermissions->merge($globalPermissions)->unique()->count() > 0;
        $isTournamentAdmin = OrganizerTournament::where('organizer_id', $this->userId)
                ->where('tournament_id', $this->tournamentId)
                ->count() > 0;
        if (!$hasPermission && !$isTournamentAdmin) {
            throw new AuthorizationException(trans('validation.forbidden'));
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.has_permission');
    }
}

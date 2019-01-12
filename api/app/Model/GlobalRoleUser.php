<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre un rôle global et un utilisateur.
 *
 * Class GlobalRoleUser
 * @package App\Model
 */
class GlobalRoleUser extends Model
{
    protected $table = 'global_role_user';
}

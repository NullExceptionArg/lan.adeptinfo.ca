<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre un rôle de LAN et un utilisateur.
 *
 * Class LanRoleUser
 * @package App\Model
 */
class LanRoleUser extends Model
{
    protected $table = 'lan_role_user';
}

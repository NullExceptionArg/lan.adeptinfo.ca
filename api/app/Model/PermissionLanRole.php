<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre une permission et un rôle de LAN.
 *
 * Class PermissionLanRole
 * @package App\Model
 */
class PermissionLanRole extends Model
{
    protected $table = 'permission_lan_role';
}

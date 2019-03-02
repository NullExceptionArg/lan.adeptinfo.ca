<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

/**
 * Lien entre une permission et un rôle global.
 *
 * Class PermissionGlobalRole
 * @package App\Model
 */
class PermissionGlobalRole extends Model
{
    protected $table = 'permission_global_role';
}

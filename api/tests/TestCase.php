<?php

namespace Tests;

use App\Model\{GlobalRole, LanRole, Permission};
use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

/**
 * Contexte de base des tests de l'API
 *
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Créer l'application
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp()
    {
        parent::setUp();
        $this->artisan('lan:permissions');
    }

    /**
     * Ajouter une permission de LAN à un utilisateur.
     *
     * @param int $userId Id de l'utilisateur
     * @param int $lanId Id du LAN
     * @param string $permissionName Nom unique de la permission
     * @return LanRole Rôle de LAN créé
     */
    public function addLanPermissionToUser(int $userId, int $lanId, string $permissionName): LanRole
    {
        $role = factory('App\Model\LanRole')->create([
            'lan_id' => $lanId
        ]);
        $permission = Permission::where('name', $permissionName)->first();

        factory('App\Model\PermissionLanRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\LanRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $userId
        ]);

        return $role;
    }

    /**
     * Ajouter une permission globale à un utilisateur.
     *
     * @param int $userId Id de l'utilisateur
     * @param string $permissionName Nom unique de la permission
     * @return GlobalRole Rôle global créé
     */
    public function addGlobalPermissionToUser(int $userId, string $permissionName): GlobalRole
    {
        $role = factory('App\Model\GlobalRole')->create();
        $permission = Permission::where('name', $permissionName)->first();

        factory('App\Model\PermissionGlobalRole')->create([
            'role_id' => $role->id,
            'permission_id' => $permission->id
        ]);
        factory('App\Model\GlobalRoleUser')->create([
            'role_id' => $role->id,
            'user_id' => $userId
        ]);

        return $role;
    }
}

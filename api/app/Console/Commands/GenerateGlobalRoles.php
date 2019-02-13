<?php

namespace App\Console\Commands;

use App\Model\Permission;
use Illuminate\{Console\Command, Support\Facades\DB};

/**
 * Générer les rôles globaux par défaut.
 * Les rôles sont définis dans dans /resources/roles.php
 *
 * Class GenerateGlobalRoles
 * @package App\Console\Commands
 */
class GenerateGlobalRoles extends Command
{
    /**
     * Nom et signature de la commande.
     *
     * @var string
     */
    protected $signature = 'lan:roles';

    /**
     * Description de la commande.
     *
     * @var string
     */
    protected $description = 'Générer les rôles généraux par défaut.';

    public function handle()
    {
        $this->preconditions();
        $this->comment('Génération des rôles globaux par défaut.');

        $lanRoles = (include(base_path() . '/resources/roles.php'))['global_roles'];
        $bar = $this->output->createProgressBar(count($lanRoles));

        foreach ($lanRoles as $role) {
            $bar->advance();
            // Créer le rôle
            $roleId = DB::table('global_role')->insertGetId([
                'name' => $role['name'],
                'en_display_name' => $role['en_display_name'],
                'en_description' => $role['en_description'],
                'fr_display_name' => $role['fr_display_name'],
                'fr_description' => $role['fr_description'],
            ]);
            // Associer chacunes des permissions du rôle au rôle créé
            foreach ($role['permissions'] as $permission) {
                DB::table('permission_global_role')->insert([
                    'permission_id' => Permission::where('name', $permission['name'])->first()->id,
                    'role_id' => $roleId
                ]);
            }
        }

        // Afficher un résumé des rôles qui ont été créés
        $bar->finish();
        $this->line('');
        $this->info('Rôles globaux par défaut générés.');
        $headers = ['id', 'name'];
        $roles = json_decode(json_encode(DB::table('global_role')->get(['id', 'name'])), true);
        $this->table($headers, $roles);
        $this->line('');
    }

    /**
     * Précondition pour pouvoir utiliser la commande :
     * Toutes les permissions de l'application doivent avoir été générées.
     */
    private function preconditions(): void
    {
        $permissions = include(base_path() . '/resources/permissions.php');
        if (Permission::all()->count() != count($permissions)) {
            $this->error('Précondition non remplie. Indice: Essayez d\'exécuter la commande "lan:permissions".');
            exit();
        }
    }
}

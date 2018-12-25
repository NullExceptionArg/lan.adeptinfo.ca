<?php

namespace App\Console\Commands;

use App\Model\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateGlobalRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lan:roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the default roles for the administration of lans.';

    public function handle()
    {
        $this->comment('Generating default global roles');
        $lanRoles = (include(base_path() . '/resources/roles.php'))['global_roles'];
        $bar = $this->output->createProgressBar(count($lanRoles));
        foreach ($lanRoles as $role) {
            $bar->advance();
            $roleId = DB::table('global_role')->insertGetId([
                'name' => $role['name'],
                'en_display_name' => $role['en_display_name'],
                'en_description' => $role['en_description'],
                'fr_display_name' => $role['fr_display_name'],
                'fr_description' => $role['fr_description'],
            ]);
            foreach ($role['permissions'] as $permission) {
                DB::table('permission_global_role')->insert([
                    'permission_id' => Permission::where('name', $permission['name'])->first()->id,
                    'role_id' => $roleId
                ]);
            }
        }
        $bar->finish();

        $this->line('');
        $this->info('Default global roles generated');
        $headers = ['id', 'name'];
        $roles = json_decode(json_encode(DB::table('global_role')->get(['id', 'name'])), true);
        $this->table($headers, $roles);
        $this->line('');
    }
}

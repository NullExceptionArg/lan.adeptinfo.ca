<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneratePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lan:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the permissions for the administration of lans.';

    public function handle()
    {
        $this->comment('Generating permissions');
        DB::table('permission')->insert(include(base_path() . '/resources/permissions.php'));
        $this->info('Permissions generated');

        $headers = ['id', 'name'];
        $roles = json_decode(json_encode(DB::table('permission')->orderBy('id')->get(['id', 'name'])), true);
        $this->table($headers, $roles);
        $this->line('');
    }
}

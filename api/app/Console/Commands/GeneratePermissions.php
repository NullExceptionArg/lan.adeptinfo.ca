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
        DB::table('permission')->insert(include(base_path() . '/resources/permissions.php'));
    }
}

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
        DB::table('permission')->insert($this->getPermissions());
    }

    public function getPermissions()
    {
        return [

            /// Route based

            // LAN
            ['name' => 'create-lan'],
            ['name' => 'set-current-lan'],
            ['name' => 'edit-lan'],

            // Contribution
            ['name' => 'create-contribution-category'],
            ['name' => 'delete-contribution-category'],
            ['name' => 'create-contribution'],
            ['name' => 'delete-contribution'],

            // Seat
            ['name' => 'confirm-arrival'],
            ['name' => 'unconfirm-arrival'],
            ['name' => 'assign-seat'],
            ['name' => 'unassign-seat'],

            // Image
            ['name' => 'add-image'],
            ['name' => 'delete-image'],

            // Tournament
            ['name' => 'create-tournament'],
            ['name' => 'edit-tournament'],
            ['name' => 'delete-tournament'],
            ['name' => 'quit-tournament'],

            // Team
            ['name' => 'delete-team'],

            // Roles
            ['name' => 'create-role'],
        ];
    }
}

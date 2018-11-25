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
            ['name' => 'create-lan', 'can_be_per_lan' => false],
            ['name' => 'set-current-lan', 'can_be_per_lan' => false],
            ['name' => 'edit-lan', 'can_be_per_lan' => true],

            // Contribution
            ['name' => 'create-contribution-category', 'can_be_per_lan' => true],
            ['name' => 'delete-contribution-category', 'can_be_per_lan' => true],
            ['name' => 'create-contribution', 'can_be_per_lan' => true],
            ['name' => 'delete-contribution', 'can_be_per_lan' => true],

            // Seat
            ['name' => 'confirm-arrival', 'can_be_per_lan' => true],
            ['name' => 'unconfirm-arrival', 'can_be_per_lan' => true],
            ['name' => 'assign-seat', 'can_be_per_lan' => true],
            ['name' => 'unassign-seat', 'can_be_per_lan' => true],

            // Image
            ['name' => 'add-image', 'can_be_per_lan' => true],
            ['name' => 'delete-image', 'can_be_per_lan' => true],

            // Tournament
            ['name' => 'create-tournament', 'can_be_per_lan' => true],
            ['name' => 'edit-tournament', 'can_be_per_lan' => true],
            ['name' => 'delete-tournament', 'can_be_per_lan' => true],
            ['name' => 'quit-tournament', 'can_be_per_lan' => true],

            // Team
            ['name' => 'delete-team', 'can_be_per_lan' => true],

            // Roles
            ['name' => 'create-lan-role', 'can_be_per_lan' => true],
            ['name' => 'edit-lan-role', 'can_be_per_lan' => true],
            ['name' => 'add-permissions-lan-role', 'can_be_per_lan' => true],
            ['name' => 'assign-lan-role', 'can_be_per_lan' => true],
            ['name' => 'create-global-role', 'can_be_per_lan' => false],
            ['name' => 'edit-global-role', 'can_be_per_lan' => false],
            ['name' => 'add-permissions-global-role', 'can_be_per_lan' => false],
            ['name' => 'assign-global-role', 'can_be_per_lan' => false],

            // User
            ['name' => 'admin-summary', 'can_be_per_lan' => true]
        ];
    }
}

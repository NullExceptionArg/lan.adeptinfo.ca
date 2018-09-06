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
    protected $description = 'Génère les permissions pour l\'administration des LANs.';

    public function handle()
    {
        DB::table('permission')->insert([

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
        ]);
    }
}

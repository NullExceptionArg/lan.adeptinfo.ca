<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Commandes Artisan de l'application.
     *
     * @var array
     */
    protected $commands = [
        Commands\GeneratePermissions::class,
        Commands\GenerateGlobalRoles::class,
        Commands\GenerateGeneralAdmin::class
    ];

    /**
     * Planification des commandes à exécuter.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}

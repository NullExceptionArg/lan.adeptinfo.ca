<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Les mappages de l'écouteur d'événements pour l'application.
     *
     * @var array
     */
    protected $listen = [
    ];
}

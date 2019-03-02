<?php

namespace App\Model;

/**
 * Énumération des états possibles pour un tournoi.
 *
 * Class TournamentState
 * @package App\Model
 */
class TournamentState
{
    // Le tournoi est caché, et est uniquement visible par les administrateurs
    const HIDDEN = "hidden";

    // Le tournoi est terminé
    const FINISHED = "finished";

    // Le tournoi est à venir
    const FOURTHCOMING = "fourthcoming";

    // Le tournoi est retard
    const LATE = "late";

    // Le tournoi est devancé, et est en cours
    const OUTGUESSED = "outguessed";

    // Le tournoi est en cours
    const RUNNING = "running";

    // Le tournoi est en retard sur l'horaire (s'éternise)
    const BEHINDHAND = "behindhand";

    // État inconnu
    const UNKNOWN = "unknown";
}
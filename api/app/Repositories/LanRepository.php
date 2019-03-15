<?php

namespace App\Repositories;

use App\Model\{Lan, LanImage};
use DateTime;
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux LANs.
 *
 * Interface LanRepository
 * @package App\Repositories
 */
interface LanRepository
{
    /**
     * Ajouter une image à un LAN.
     *
     * @param int $lanId Id du LAN dans lequel l'image sera ajoutée.
     * @param string $image Image à ajouter, en base64
     * @return int Id de l'image ajoutée.
     */
    public function addLanImage(int $lanId, string $image): int;

    /**
     * Créer un LAN.
     *
     * @param string $name Nom du LAN.
     * @param DateTime $lanStart Date et heure de début du LAN.
     * @param DateTime $lanEnd Date et heure de fin du LAN.
     * @param DateTime $seatReservationStart Date et heure de début de réservation des places.
     * @param DateTime $tournamentReservationStart Date et heure de début d'inscription des tournois.
     * @param string $eventKey Clé d'événement de seats.io.
     * @param float $latitude Latitude de l'emplacement où se déroulera le LAN.
     * @param float $longitude Longitude de l'emplacement où se déroulera le LAN.
     * @param int $places Nombre de places disponibles pour le LAN.
     * @param bool $isCurrent Si le LAN est courant.
     * @param int|null $price Prix d'entrée du LAN. (Optionnel)
     * @param string|null $rules Règles du LAN. (Optionnel)
     * @param string|null $description Description du LAN. (Optionnel)
     * @return int Id du LAN créé.
     */
    public function create(
        string $name,
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKey,
        float $latitude,
        float $longitude,
        int $places,
        bool $isCurrent,
        ?int $price,
        ?string $rules,
        ?string $description
    ): int;

    /**
     * Supprimer une image d'un LAN.
     *
     * @param array $imageId Id de l'image à supprimer.
     */
    public function deleteLanImages(array $imageId): void;

    /**
     * Trouver un LAN.
     *
     * @param int $id Id du LAN à trouver.
     * @return Lan|null LAN trouvé, ou null si rien n'a été trouvé.
     */
    public function findById(int $id): ?Lan;

    /**
     * Trouver une image d'un LAN.
     *
     * @param int $imageId Id de l'image du LAN.
     * @return LanImage|null Image trouvée, ou null si rien n'a été trouvé.
     */
    public function findLanImageById(int $imageId): ?LanImage;

    /**
     * Obtenir tous les LANs de l'application.
     *
     * @return Collection|null LANs trouvés, null si rien n'a été trouvé.
     */
    public function getAll(): ?Collection;

    /**
     * Obtenir les images d'un LAN.
     *
     * @param int $lanId Id du LAN pour lequel les images seront retournées.
     * @return Collection Images trouvées.
     */
    public function getImagesForLan(int $lanId): Collection;

    /**
     * Retirer l'attribut courant de tous les LANs.
     */
    public function removeCurrent(): void;

    /**
     * Rendre un LAN comme courant.
     *
     * @param string $lanId Id du LAN à mettre comme courant.
     */
    public function setCurrent(string $lanId): void;

    /**
     * Mettre à jour les informations d'un LAN.
     *
     * @param int $lanId Id du LAN à mettre à jour.
     * @param string|null $name Nouveau nom du LAN. (Optionnel)
     * @param DateTime|null $lanStart Nouvelle date et heure de départ du LAN. (Optionnel)
     * @param DateTime|null $lanEnd Nouvelle date et heure de fin du LAN. (Optionnel)
     * @param DateTime|null $seatReservationStart Nouvelle date et heure de début des réservations du LAN. (Optionnel)
     * @param DateTime|null $tournamentReservationStart Nouvelle date et heure de début des inscriptions aux tournois du LAN. (Optionnel)
     * @param string|null $eventKey Nouvelle clé d'événement de seats.io du LAN. (Optionnel)
     * @param float|null $latitude Nouvelle latitude de l'emplacement du LAN. (Optionnel)
     * @param float|null $longitude Nouvelle longitude de l'emplacement du LAN. (Optionnel)
     * @param int|null $places Nouveau nombre de place maximal du LAN. (Optionnel)
     * @param int|null $price Nouveau prix d'entrée du LAN.
     * @param string|null $rules Nouveaux règlements du LAN. (Optionnel)
     * @param string|null $description Nouvelle description du LAN. (Optionnel)
     */
    public function update(
        int $lanId,
        ?string $name,
        ?DateTime $lanStart,
        ?DateTime $lanEnd,
        ?DateTime $seatReservationStart,
        ?DateTime $tournamentReservationStart,
        ?string $eventKey,
        ?float $latitude,
        ?float $longitude,
        ?int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): void;
}

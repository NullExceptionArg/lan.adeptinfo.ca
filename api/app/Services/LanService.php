<?php

namespace App\Services;

use App\Http\Resources\{Lan\GetResource, Lan\ImageResource, Lan\UpdateResource};
use App\Model\Lan;
use DateTime;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Méthodes pour exécuter la logique d'affaire des LANs.
 *
 * Interface LanService
 * @package App\Services
 */
interface LanService
{
    /**
     * Ajouter une image à un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $image Image encodée en base64
     * @return ImageResource Image créée
     */
    public function addLanImage(int $lanId, string $image): ImageResource;

    /**
     * Créer un LAN.
     *
     * @param string $name Nom du LAN
     * @param DateTime $lanStart Date et heure de début du LAN
     * @param DateTime $lanEnd Date et heure de fin du LAN
     * @param DateTime $seatReservationStart Date et heure de début de la réservation des places
     * @param DateTime $tournamentReservationStart Date et heure de début de l'inscription aux tournois
     * @param string $eventKey Clé d'événement seats.io
     * @param string $publicKey Clé publique seats.io
     * @param string $secretKey Clé secrète seats.io
     * @param float $latitude Latitude des coordonnées géographiques du LAN
     * @param float $longitude Longitude des coordonnées géographiques du LAN
     * @param int $places Nombre de place disponibles pour le LAN
     * @param int|null $price Prix d'entrée des places du LAN
     * @param string|null $rules Règles du LAN
     * @param string|null $description Description du LAN
     * @return Lan LAN créé
     */
    public function create(
        string $name,
        DateTime $lanStart,
        DateTime $lanEnd,
        DateTime $seatReservationStart,
        DateTime $tournamentReservationStart,
        string $eventKey,
        string $publicKey,
        string $secretKey,
        float $latitude,
        float $longitude,
        int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): Lan;

    /**
     * Supprimer des images d'un LAN
     *
     * @param string $imageIds Ids des images à supprimer, séparés par des virgules
     * @return array Tableau contenant les id des images supprimées
     */
    public function deleteLanImages(string $imageIds): array;

    /**
     * Obtenir tout les LANs de l'API.
     *
     * @return ResourceCollection LANs de l'API
     */
    public function getAll(): ResourceCollection;

    /**
     * Obtenir les détails d'un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string|null $fields Champs à afficher
     * @return GetResource Détails du LAN
     */
    public function get(int $lanId, ?string $fields): GetResource;

    /**
     * Mettre un LAN comme courant.
     *
     * @param int $lanId Id du LAN
     * @return Lan LAN mis comme courant
     */
    public function setCurrent(int $lanId): Lan;

    /**
     * Mettre à jour les détails d'un LAN.
     * Si un champ est nul, l'ancienne valeur sera gardée.
     *
     * @param int $lanId Id du LAN à mettre à jour
     * @param string|null $name Nouveau nom
     * @param DateTime|null $lanStart Nouvelle date et heure de début du LAN
     * @param DateTime|null $lanEnd Nouvelle date et heure de fin du LAN
     * @param DateTime|null $seatReservationStart Nouvelle date et heure du début de la réservation des sièges
     * @param DateTime|null $tournamentReservationStart Nouvelle date et heure de début d'inscription aux tournois
     * @param string|null $eventKey Nouvelle clé d'événement seats.io
     * @param string|null $publicKey Nouvelle clé publique seats.io
     * @param string|null $secretKey Nouvelle Nouvelle clé secrète seats.io
     * @param float|null $latitude Nouvelle latitude des coordonnées
     * @param float|null $longitude Nouvelle longitude des coordonnées
     * @param int|null $places Nouveau nombre de places disponibles pour le LAN
     * @param int|null $price Nouveau prix d'entrée des places
     * @param string|null $rules Nouveaux règlements du LAN
     * @param string|null $description Nouvelle description du LAN
     * @return UpdateResource LAN mis à jour
     */
    public function update(
        int $lanId,
        ?string $name,
        ?DateTime $lanStart,
        ?DateTime $lanEnd,
        ?DateTime $seatReservationStart,
        ?DateTime $tournamentReservationStart,
        ?string $eventKey,
        ?string $publicKey,
        ?string $secretKey,
        ?float $latitude,
        ?float $longitude,
        ?int $places,
        ?int $price,
        ?string $rules,
        ?string $description
    ): UpdateResource;
}

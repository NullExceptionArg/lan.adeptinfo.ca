<?php

namespace App\Repositories;

use App\Model\Reservation;
use Illuminate\Support\Collection;

/**
 * Méthodes pour accéder aux tables de base de donnée liées aux places.
 *
 * Interface SeatRepository
 * @package App\Repositories
 */
interface SeatRepository
{
    /**
     * Créer une réservation
     *
     * @param int $userId Id de l'utilisateur qui réserve la place.
     * @param int $lanId Id du LAN dans lequel la réservation a lieu.
     * @param string $seatId Id du siège réservée (Dans seats.io).
     * @return int Id de la place réservée.
     */
    public function createReservation(int $userId, int $lanId, string $seatId): int;

    /**
     * Supprimer une réservation.
     *
     * @param int $reservationId
     */
    public function deleteReservation(int $reservationId): void;

    /**
     * Trouver une réservation par le LAN et le siège.
     *
     * @param int $lanId Id du LAN.
     * @param string $seatId Id du siège (Dans seats.io).
     * @return Reservation|null Réservation trouvée, null si rien n'a été trouvé.
     */
    public function findReservationByLanIdAndSeatId(int $lanId, string $seatId): ?Reservation;

    /**
     * Trouver une réservation par le LAN et l'utilisateur qui la possède.
     *
     * @param int $lanId Id du LAN.
     * @param int $userId Id de l'utilisateur.
     * @return Reservation|null Réservation trouvée, null si rien n'a été trouvé.
     */
    public function findReservationByLanIdAndUserId(int $lanId, int $userId): ?Reservation;

    /**
     * Obtenir le nombre de place occupées dans un LAN.
     *
     * @param int $lanId Id du LAN.
     * @return int Nombre de places occupées.
     */
    public function getReservedPlaces(int $lanId): int;

    /**
     * Obtenir l'historique des places pour un utilisateur, dans un LAN.
     *
     * @param int $userId Id de l'utilisateur.
     * @param int $lanId Id du LAN.
     * @return Collection|null Ensemble des réservation qui ont été effectuées.
     */
    public function getSeatHistoryForUser(int $userId, int $lanId): ?Collection;

    /**
     * Ajouter une date d'arrivée à une réservation, ce qui signifie que l'utilisateur est arrivé sur place.
     *
     * @param string $reservationId Id de la réservation.
     * @param int $lanId Id du LAN de la réservation.
     */
    public function setReservationArrived(string $reservationId, int $lanId): void;

    /**
     * Ajouter une date de départ à une réservation, ce qui signifie que l'utilisateur a quitté l'événement.
     * La place est toujours réservée pour l'utilisateur.
     *
     * @param string $reservationId Id de la réservation.
     * @param int $lanId Id du LAN de la réservation.
     */
    public function setReservationLeft(string $reservationId, int $lanId): void;
}

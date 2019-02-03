<?php

namespace App\Services;

use App\Model\Reservation;

/**
 * Méthodes pour exécuter la logique d'affaire des sièges.
 *
 * Interface SeatService
 * @package App\Services<
 */
interface SeatService
{
    /**
     * Assigner un siège à un utilisateur, dans un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $email Courriel de l'utilisateur
     * @param string $seatId Id du siège à assigner
     * @return Reservation Réservation effectuée
     */
    public function assign(int $lanId, string $email, string $seatId): Reservation;

    /**
     * Réserver un siège, dans un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $seatId Id du siège à réserver
     * @return Reservation Réservation effectuée
     */
    public function book(int $lanId, string $seatId): Reservation;

    /**
     * Confirmer l'arrivée sur place d'un utilisateur, à un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $seatId Id du siège à confirmer
     * @return Reservation Réservation confirmée
     */
    public function confirmArrival(int $lanId, string $seatId): Reservation;

    /**
     * Déassigner un siège à un utilisateur, dans un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $email Courriel de l'utilisateur
     * @param string $seatId Id du siège à déassigner
     * @return Reservation Réservation désassigné
     */
    public function unAssign(int $lanId, string $email, string $seatId): Reservation;

    /**
     * Annuler une réservation à un LAN
     *
     * @param int $lanId Id du LAN
     * @param string $seatId Id du siège de la réservation
     * @return Reservation Réservation annulée
     */
    public function unBook(int $lanId, string $seatId): Reservation;

    /**
     * Déconfirmer l'arrivée d'un utilisateur à un LAN.
     *
     * @param int $lanId Id du LAN
     * @param string $seatId Id du siège à annuler
     * @return Reservation Réservation déconfirmée
     */
    public function unConfirmArrival(int $lanId, string $seatId): Reservation;
}

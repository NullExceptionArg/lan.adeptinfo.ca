<?php

namespace App\Services;

use Seatsio\Charts\ChartPage;

/**
 * Méthodes pour exécuter la logique d'affaire des sièges.
 *
 * Interface SeatService
 */
interface SeatService
{
    /**
     * Assigner un siège à un utilisateur, dans un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $email  Courriel de l'utilisateur
     * @param string $seatId Id du siège à assigner
     *
     * @return string Id du siège de la réservation effectuée
     */
    public function assign(int $lanId, string $email, string $seatId): string;

    /**
     * Réserver un siège, dans un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $seatId Id du siège à réserver
     * @param int    $userId Id de l'utilisateur qui réserve le siège
     *
     * @return string Id du siège de la réservation effectuée
     */
    public function book(int $lanId, string $seatId, int $userId): string;

    /**
     * Confirmer l'arrivée sur place d'un utilisateur, à un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $seatId Id du siège à confirmer
     *
     * @return string Id du siège de la réservation confirmée
     */
    public function confirmArrival(int $lanId, string $seatId): string;

    /**
     * Lister les cartes de seats.io ainsi que les événements qui leurs sont rattachés.
     *
     * @return ChartPage Liste des cartes de seats.io
     */
    public function getSeatCharts(): ChartPage;

    /**
     * Déassigner un siège à un utilisateur, dans un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $email  Courriel de l'utilisateur
     * @param string $seatId Id du siège à déassigner
     *
     * @return string Id du siège de la réservation désassigné
     */
    public function unAssign(int $lanId, string $email, string $seatId): string;

    /**
     * Annuler une réservation à un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $seatId Id du siège de la réservation
     * @param int    $userId Id de l'utilisateur qui annule sa réservation
     *
     * @return string Id du siège de la réservation annulée
     */
    public function unBook(int $lanId, string $seatId, int $userId): string;

    /**
     * Déconfirmer l'arrivée d'un utilisateur à un LAN.
     *
     * @param int    $lanId  Id du LAN
     * @param string $seatId Id du siège à annuler
     *
     * @return string Id du siège de la réservation déconfirmée
     */
    public function unConfirmArrival(int $lanId, string $seatId): string;
}

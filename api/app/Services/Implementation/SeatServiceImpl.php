<?php

namespace App\Services\Implementation;

use App\Model\Reservation;
use App\Repositories\Implementation\{LanRepositoryImpl, SeatRepositoryImpl, UserRepositoryImpl};
use App\Services\SeatService;
use Seatsio\SeatsioClient;

class SeatServiceImpl implements SeatService
{
    protected $lanRepository;
    protected $seatRepository;
    protected $userRepository;

    /**
     * SeatServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param SeatRepositoryImpl $seatRepositoryImpl
     * @param UserRepositoryImpl $userRepositoryImpl
     */
    public function __construct(
        LanRepositoryImpl $lanRepositoryImpl,
        SeatRepositoryImpl $seatRepositoryImpl,
        UserRepositoryImpl $userRepositoryImpl
    )
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->seatRepository = $seatRepositoryImpl;
        $this->userRepository = $userRepositoryImpl;
    }

    public function assign(int $lanId, string $email, string $seatId): Reservation
    {
        // Trouver l'utilisateur correpondant au courriel
        $user = $this->userRepository->findByEmail($email);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Créer un objet pour la place, au nom de l'utilisateur, avec son courriel et son nom complet
        $seatObject = [[
            'objectId' => $seatId,
            'extraData' => [
                'name' => $user->getFullName(),
                'email' => $user->email
            ]
        ]];

        // Effectuer la réservation dans l'API de seats.io
        $seatsClient->events->book($lan->event_key, $seatObject);

        // Créer la réservation
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        // Trouver et retourner la réservation créée
        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function book(int $lanId, string $seatId, int $userId): Reservation
    {
        // Trouver l'utilisateur
        $user = $this->userRepository->findById($userId);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Créer un objet pour la place, au nom de l'utilisateur, avec son courriel et son nom complet
        $seatObject = [[
            'objectId' => $seatId,
            'extraData' => [
                'name' => $user->getFullName(),
                'email' => $user->email]
        ]];

        // Effectuer la réservation dans l'API de seats.io
        $seatsClient->events->book($lan->event_key, $seatObject);

        // Créer la réservation
        $this->seatRepository->createReservation($user->id, $lan->id, $seatId);

        // Trouver et retourner la réservation créée
        return $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);
    }

    public function confirmArrival(int $lanId, string $seatId): Reservation
    {
        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Trouver la réservation qui correspond au siège et au LAN
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        // Mettre le status du siège à "arrived" dans l'API seats.io
        $seatsClient->events->changeObjectStatus($lan->event_key, [$seatId], "arrived");

        // Mettre le statut de la place à arrivé
        $this->seatRepository->setReservationArrived($reservation->id, $lan->id);

        // Retourner la réservation modifiée
        return $reservation;
    }

    public function unAssign(int $lanId, string $email, string $seatId): Reservation
    {
        // Trouver l'utilisateur qui correspond au courriel
        $user = $this->userRepository->findByEmail($email);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Trouver la réservation qui correspond au siège et au LAN
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);

        // Rendre le siège libre dans l'API seats.io
        $seatsClient->events->release($lan->event_key, [$seatId]);

        // Supprimer la réservation
        $this->seatRepository->deleteReservation($reservation->id);

        // Retourner la réservation supprimée
        return $reservation;
    }

    public function unBook(int $lanId, string $seatId, int $userId): Reservation
    {
        // Trouver l'utilisateur
        $user = $this->userRepository->findById($userId);

        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Trouver la réservation qui correspond au siège et au LAN
        $reservation = $this->seatRepository->findReservationByLanIdAndUserId($lan->id, $user->id);

        // Rendre le siège libre dans l'API seats.io
        $seatsClient->events->release($lan->event_key, [$seatId]);

        // Supprimer la réservation
        $this->seatRepository->deleteReservation($reservation->id);

        // Retourner la réservation supprimée
        return $reservation;
    }

    public function unConfirmArrival(int $lanId, string $seatId): Reservation
    {
        // Trouver le LAN
        $lan = $this->lanRepository->findById($lanId);

        // Créer un client seats.io
        $seatsClient = new SeatsioClient($lan->secret_key);

        // Trouver la réservation qui correspond au siège et au LAN
        $reservation = $this->seatRepository->findReservationByLanIdAndSeatId($lan->id, $seatId);

        // Changer le status du siège à "booked" dans seats.io, ce qui signifie que la place est réservée, mais
        // que le joueur n'est toujours pas arrivé
        $seatsClient->events->changeObjectStatus($lan->event_key, [$seatId], "booked");

        // Mettre le statut de la place à réservé
        $this->seatRepository->setReservationLeft($reservation->id, $lan->id);

        // Retourner la réservation
        return $reservation;
    }
}

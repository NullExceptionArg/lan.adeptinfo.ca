<?php

namespace App\Services\Implementation;

use App\Http\Resources\{Team\GetRequestsResource, Team\GetUsersTeamDetailsResource, Team\GetUserTeamsResource};
use App\Model\{Request as TeamRequest, Tag, Team};
use App\Repositories\Implementation\{LanRepositoryImpl, TeamRepositoryImpl, TournamentRepositoryImpl};
use App\Services\TeamService;
use Illuminate\{Http\Resources\Json\AnonymousResourceCollection};

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;
    protected $lanRepository;

    /**
     * TeamServiceImpl constructor.
     * @param TeamRepositoryImpl $teamRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(
        TeamRepositoryImpl $teamRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl,
        LanRepositoryImpl $lanRepositoryImpl
    )
    {
        $this->teamRepository = $teamRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function acceptRequest(int $requestId): Tag
    {
        // Trouver la requête
        $request = $this->teamRepository->findRequestById($requestId);

        // Trouver le tag de la requête
        $tag = $this->teamRepository->findTagById($request->tag_id);

        // Trouver l'équipe de la requête
        $team = $this->teamRepository->findById($request->team_id);

        // Lier le tag de l'utilisateur à l'équipe
        $this->teamRepository->linkTagTeam($tag->id, $team->id, false);

        // Supprimer la requête
        $this->teamRepository->deleteRequest($request->id);

        // Retourner le tag de l'utilisateur faisant maintenant parti de l'équipe
        return $tag;
    }

    public function changeLeader(int $tagId, int $teamId): Tag
    {
        // Trouver le tag
        $tag = $this->teamRepository->findTagById($tagId);

        // Changer de chef de l'équipe pour celui du tag
        $this->teamRepository->switchLeader($tag->id, $teamId);

        // Retourner le tag du nouveau chef
        return $tag;
    }

    public function createRequest(int $teamId, int $tagId): TeamRequest
    {
        // Créer une requête pour le tag et l'équipe
        $requestId = $this->teamRepository->createRequest($teamId, $tagId);

        // Trouver et retourner la requête créée
        return $this->teamRepository->findRequestById($requestId);
    }

    public function create(int $tournamentId, string $name, string $tag, int $userTagId): Team
    {
        // Créer une équipe
        $teamId = $this->teamRepository->create(
            $tournamentId,
            $name,
            $tag
        );

        // Lier l'équipe au tournoi
        $this->teamRepository->linkTagTeam($userTagId, $teamId, true);

        // Trouver et retourner l'équipe trouvée
        return $this->teamRepository->findById($teamId);
    }

    public function delete(int $teamId): Team
    {
        // Trouver l'équipe
        $team = $this->teamRepository->findById($teamId);

        // Supprimer l'équipe
        $this->teamRepository->delete($teamId);

        // Retourner l'équipe supprimée
        return $team;
    }

    public function deleteRequestLeader(int $requestId): Tag
    {
        // Trouver la requête
        $request = $this->teamRepository->findRequestById($requestId);

        // Trouver le tag du joueur
        $tag = $this->teamRepository->findTagById($request->tag_id);

        // Supprimer la requête
        $this->teamRepository->deleteRequest($requestId);

        // Retourner le tag du joueur dont la requête pour entrer dans une équipe a été supprimée
        return $tag;
    }

    public function deleteRequestPlayer(int $requestId): Team
    {
        // Trouver la requête
        $request = $this->teamRepository->findRequestById($requestId);

        // Trouver l'équipe
        $team = $this->teamRepository->findById($request->team_id);

        // Supprimer la requête
        $this->teamRepository->deleteRequest($requestId);

        // Retourner l'équipe de la requête supprimée
        return $team;
    }

    public function getRequests(int $userId, int $lanId): AnonymousResourceCollection
    {
        return GetRequestsResource::collection($this->teamRepository->getRequestsForUser($userId, $lanId));
    }

    public function getUsersTeamDetails(int $userId, int $teamId): GetUsersTeamDetailsResource
    {
        // Trouver l'équipe
        $team = $this->teamRepository->findById($teamId);

        //Déterminer si l'utilisateur qui fait la requête est le chef de l'équipe
        $isLeader = $this->teamRepository->userIsLeader($teamId, $userId);

        // Obtenir les tags des utilisateurs qui font parti de l'équipe
        $tags = $this->teamRepository->getUsersTeamTags($teamId);

        $requests = null;

        // Si l'utilisateur qui fait la requête est chef de l'équipe
        if ($isLeader) {
            // Inclure les requêtes pour entrer dans l'équipe
            $requests = $this->teamRepository->getRequests($teamId);
        }

        // Retourner les détails de l'équipe de l'utilisateur
        return new GetUsersTeamDetailsResource($team, $tags, $requests);
    }

    public function getUserTeams(int $userId, int $lanId): AnonymousResourceCollection
    {
        // Trouver les équipes de l'utilisateur
        $teams = $this->teamRepository->getUserTeams($userId, $lanId);

        // Retourner les équipes de l'utilisateur
        return GetUserTeamsResource::collection($teams);
    }

    public function kick(int $teamId, int $tagId): Tag
    {
        // Supprimer le lien entre le tag du joueur et l'équipe
        $this->teamRepository->deleteTagTeam($tagId, $teamId);

        // Trouver le tag du joueur
        $tag = $this->teamRepository->findTagById($tagId);

        // Retourner le tag du joueur qui a été exclu de l'équipe
        return $tag;
    }

    public function leave(int $userId, int $teamId): Team
    {
        // Trouver l'équipe
        $team = $this->teamRepository->findById($teamId);

        // Si le joueur est le chef de l'équipe
        if ($this->teamRepository->userIsLeader($teamId, $userId)) {
            // Obtenir le tag du joueur ayant le plus de séniorité dans l'équipe, et n'étant pas le chef
            $tag = $this->teamRepository->getTagWithMostSeniorityNotLeader($teamId);

            // Si aucun tag n'a été trouvé (il ne reste plus de joueurs dans l'équipe)
            if (is_null($tag)) {
                // Supprimer l'équipe
                $this->teamRepository->delete($teamId);
            } else {
                // Donner le rôle de chef au tag de joueur ayant le plus d'ancienneté
                $this->teamRepository->switchLeader($tag->id, $teamId);
            }
        }

        // Supprimer le lien entre le tag du joueur et l'équipe
        $this->teamRepository->removeUserFromTeam($userId, $teamId);

        // Retourner l'équipe que le joueur a quitté
        return $team;
    }
}

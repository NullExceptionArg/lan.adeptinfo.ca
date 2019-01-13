<?php

namespace App\Repositories\Implementation;

use App\Model\{OrganizerTournament, TagTeam, Team, Tournament};
use App\Repositories\TournamentRepository;
use DateTime;
use Illuminate\{Support\Collection, Support\Facades\DB};

class TournamentRepositoryImpl implements TournamentRepository
{
    public function adminHasTournaments(int $userId, int $lanId): bool
    {
        return DB::table('organizer_tournament')
                ->join('tournament', 'organizer_tournament.tournament_id', '=', 'tournament.id')
                ->where('organizer_tournament.organizer_id', $userId)
                ->where('tournament.lan_id', $lanId)
                ->count() > 0;
    }

    public function associateOrganizerTournament(int $organizerId, int $tournamentId): void
    {
        DB::table('organizer_tournament')->insert([
            'organizer_id' => $organizerId,
            'tournament_id' => $tournamentId
        ]);
    }

    public function create(
        int $lanId,
        string $name,
        DateTime $tournamentStart,
        DateTime $tournamentEnd,
        int $playersToReach,
        int $teamsToReach,
        string $rules,
        ?int $price
    ): int
    {
        return DB::table('tournament')->insertGetId([
            'lan_id' => $lanId,
            'name' => $name,
            'tournament_start' => $tournamentStart->format('Y-m-d H:i:s'),
            'tournament_end' => $tournamentEnd->format('Y-m-d H:i:s'),
            'players_to_reach' => $playersToReach,
            'teams_to_reach' => $teamsToReach,
            'rules' => $rules,
            'price' => $price
        ]);
    }

    public function delete(int $tournamentId): void
    {
        $tournament = Tournament::find($tournamentId);
        $tournament->delete();
    }

    public function deleteTournamentOrganizer(int $tournamentId, int $userId): void
    {
        OrganizerTournament::where('organizer_id', $userId)
            ->where('tournament_id', $tournamentId)
            ->delete();
    }

    public function findById(int $id): ?Tournament
    {
        return Tournament::find($id);
    }

    public function getAllTournaments(int $lanId): Collection
    {
        return Tournament::where('lan_id', $lanId)->get();
    }

    public function getOrganizerCount(int $tournamentId): int
    {
        return OrganizerTournament::where('tournament_id', $tournamentId)
            ->count();
    }

    public function getReachedTeams(int $tournamentId): int
    {
        // Trouver le tournoi
        $tournament = Tournament::find($tournamentId);

        // Trouver les équipes du tournoi
        $teams = Team::where('tournament_id', $tournamentId)->get();

        // Pour chaque équipe
        $teamsReached = 0;
        foreach ($teams as $team) {
            // Trouver le nombre de joueur atteints (tags de joueur associés à l'équipe)
            $playersReached = TagTeam::where('team_id', $team->id)->count();

            // Si le nombre de joueur atteints est plus grands que le nombre de joueurs à atteindre
            if ($playersReached >= $tournament->players_to_reach) {
                // Augmenter le compteur d'équipes complètes
                $teamsReached++;
                break;
            }
        }
        return $teamsReached;
    }

    public function getTournamentsForOrganizer(int $userId, int $lanId): Collection
    {
        // Trouver l'id des tournois de l'organisateur
        $tournamentIds = DB::table('organizer_tournament')
            ->select('tournament_id')
            ->where('organizer_id', $userId)
            ->pluck('tournament_id')
            ->toArray();

        // Retourner les tournois correspondants aux id trouvés
        return Tournament::where('lan_id', $lanId)
            ->whereIn('id', $tournamentIds)
            ->get();
    }

    public function getTournamentsLanId(int $tournamentId): ?int
    {
        $tournament = Tournament::find($tournamentId);
        return !is_null($tournament) ? $tournament->lan_id : null;
    }

    public function update(
        int $tournamentId,
        ?string $name,
        ?string $state,
        ?DateTime $tournamentStart,
        ?DateTime $tournamentEnd,
        ?int $playersToReach,
        ?int $teamsToReach,
        ?string $rules,
        ?int $price
    ): void
    {
        $tournament = Tournament::find($tournamentId);
        DB::table('tournament')
            ->where('id', $tournamentId)
            ->update([
                'name' => $name != null ? $name : $tournament->name,
                'state' => $state != null ? $state : $tournament->state,
                'tournament_start' => $tournamentStart != null ? $tournamentStart->format('Y-m-d H:i:s') : $tournament->tournament_start->format('Y-m-d H:i:s'),
                'tournament_end' => $tournamentEnd != null ? $tournamentEnd->format('Y-m-d H:i:s') : $tournament->tournament_end->format('Y-m-d H:i:s'),
                'players_to_reach' => $playersToReach != null ? $playersToReach : $tournament->players_to_reach,
                'teams_to_reach' => $teamsToReach != null ? $teamsToReach : $tournament->teams_to_reach,
                'rules' => $rules != null ? $rules : $tournament->rules,
                'price' => is_null($price) ? $tournament->price : $price
            ]);
    }
}

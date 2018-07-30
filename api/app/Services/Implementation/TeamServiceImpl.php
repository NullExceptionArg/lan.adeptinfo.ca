<?php

namespace App\Services\Implementation;


use App\Model\Team;
use App\Repositories\Implementation\TagRepositoryImpl;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Rules\Team\UniqueTeamNamePerTournament;
use App\Rules\Team\UniqueTeamTagPerTournament;
use App\Rules\Team\UniqueUserPerRequest;
use App\Rules\Team\UniqueUserPerTournament;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;
    protected $tagRepository;

    /**
     * LanServiceImpl constructor.
     * @param TeamRepositoryImpl $teamRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     * @param TagRepositoryImpl $tagRepositoryImpl
     */
    public function __construct(
        TeamRepositoryImpl $teamRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl,
        TagRepositoryImpl $tagRepositoryImpl
    )
    {
        $this->teamRepository = $teamRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
        $this->tagRepository = $tagRepositoryImpl;
    }

    public function create(Request $input): Team
    {
        $tournamentValidator = Validator::make([
            'tournament_id' => $input->input('tournament_id'),
            'user_tag_id' => $input->input('user_tag_id'),
            'name' => $input->input('name'),
            'tag' => $input->input('tag')
        ], [
            'tournament_id' => 'required|exists:tournament,id',
            'user_tag_id' => ['required', 'exists:tag,id', new UniqueUserPerTournament($input->input('tournament_id'), null)],
            'name' => ['required', 'string', 'max:255', new UniqueTeamNamePerTournament($input->input('tournament_id'))],
            'tag' => ['string', 'max:5', new UniqueTeamTagPerTournament($input->input('tournament_id'))]
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findTournamentById($input->input('tournament_id'));
        $team = $this->teamRepository->create(
            $tournament,
            $input->input('name'),
            $input->input('tag')
        );

        $tag = $this->tagRepository->findTagById($input->input('user_tag_id'));
        $this->teamRepository->linkTagTeam($tag, $team, true);

        return $team;
    }

    public function createRequest(Request $input): \App\Model\Request
    {
        $tournamentValidator = Validator::make([
            'team_id' => $input->input('team_id'),
            'tag_id' => $input->input('tag_id'),
        ], [
            'team_id' => ['required', 'exists:team,id', new UniqueUserPerRequest()],
            'tag_id' => ['required', 'exists:tag,id', new UniqueUserPerTournament(null, $input->input('team_id'))],
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        return $this->teamRepository->createRequest($input->input('team_id'), $input->input('tag_id'));
    }
}
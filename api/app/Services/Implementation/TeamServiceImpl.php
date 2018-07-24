<?php

namespace App\Services\Implementation;


use App\Model\Team;
use App\Repositories\Implementation\TeamRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Services\TeamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TeamServiceImpl implements TeamService
{
    protected $teamRepository;
    protected $tournamentRepository;

    /**
     * LanServiceImpl constructor.
     * @param TeamRepositoryImpl $teamRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     */
    public function __construct(
        TeamRepositoryImpl $teamRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl
    )
    {
        $this->teamRepository = $teamRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
    }

    // TODO Documentation
    // TODO Tests
    public function create(Request $input): Team
    {
        $tournamentValidator = Validator::make([
            'tournament_id' => $input->input('tournament_id'),
            'user_tag_id' => $input->input('user_tag_id'),
            'name' => $input->input('name'),
            'team_tag' => $input->input('team_tag')
        ], [
            'tournament_id' => 'required|exists:tournament,id',
            'user_tag_id' => 'required|exists:tag,id',
            'name' => 'required|string|max:255|unique:team,name',
            'team_tag' => 'string|max:5'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findTournamentById($input->input('tournament_id'));
        $team = $this->teamRepository->create(
            $tournament,
            $input->input('name'),
            $input->input('team_tag')
        );
        // TODO team_tag unique par LAN
        // TODO Lier l'équipe et le tag


        return $team;
    }
}
<?php

namespace App\Services\Implementation;


use App\Http\Resources\Lan\GetAllTournamentResource;
use App\Model\Tournament;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Rules\AfterOrEqualLanStartTime;
use App\Rules\BeforeOrEqualLanEndTime;
use App\Rules\PlayersToReachLock;
use App\Services\TournamentService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TournamentServiceImpl implements TournamentService
{
    protected $lanRepository;
    protected $tournamentRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     * @param TournamentRepositoryImpl $tournamentRepositoryImpl
     */
    public function __construct(
        LanRepositoryImpl $lanRepositoryImpl,
        TournamentRepositoryImpl $tournamentRepositoryImpl
    )
    {
        $this->lanRepository = $lanRepositoryImpl;
        $this->tournamentRepository = $tournamentRepositoryImpl;
    }

    public function create(Request $input): Tournament
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrentLan();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $tournamentValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'name' => $input->input('name'),
            'price' => $input->input('price'),
            'tournament_start' => $input->input('tournament_start'),
            'tournament_end' => $input->input('tournament_end'),
            'players_to_reach' => $input->input('players_to_reach'),
            'teams_to_reach' => $input->input('teams_to_reach'),
            'rules' => $input->input('rules'),
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'name' => 'required|string|max:255',
            'price' => 'integer|min:0',
            'tournament_start' => ['required', new AfterOrEqualLanStartTime($input->input('lan_id'))],
            'tournament_end' => ['required', 'after:tournament_start', new BeforeOrEqualLanEndTime($input->input('lan_id'))],
            'players_to_reach' => 'required|min:1|integer',
            'teams_to_reach' => 'required|min:1|integer',
            'rules' => 'required|string'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findLanById($input->input('lan_id'));
        }

        $tournament = $this->tournamentRepository->create(
            $lan,
            $input->input('name'),
            new DateTime($input->input('tournament_start')),
            new DateTime($input->input('tournament_end')),
            $input->input('players_to_reach'),
            $input->input('teams_to_reach'),
            $input->input('rules'),
            intval($input->input('price'))
        );

        $this->tournamentRepository->associateOrganizerTournament(Auth::user(), $tournament);

        return $tournament;
    }

    public function getAll(Request $input): AnonymousResourceCollection
    {
        $lan = null;
        if ($input->input('lan_id') == null) {
            $lan = $this->lanRepository->getCurrentLan();
            $input['lan_id'] = $lan != null ? $lan->id : null;
        }

        $tournamentValidator = Validator::make([
            'lan_id' => $input->input('lan_id')
        ], [
            'lan_id' => 'integer|exists:lan,id'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        if ($lan == null) {
            $lan = $this->lanRepository->findLanById($input->input('lan_id'));
        }

        $tournaments = $this->tournamentRepository->getTournamentForOrganizer(Auth::user(), $lan);

        return GetAllTournamentResource::collection($tournaments);
    }

    public function edit(Request $input, string $tournamentId): Tournament
    {
        $tournamentValidator = Validator::make([
            'tournament_id' => $tournamentId,
            'name' => $input->input('name'),
            'state' => $input->input('state'),
            'price' => $input->input('price'),
            'tournament_start' => $input->input('tournament_start'),
            'tournament_end' => $input->input('tournament_end'),
            'players_to_reach' => $input->input('players_to_reach'),
            'teams_to_reach' => $input->input('teams_to_reach'),
            'rules' => $input->input('rules'),
        ], [
            'tournament_id' => 'integer|exists:tournament,id',
            'name' => 'nullable,string|max:255',
            'state' => ['nullable', 'string', Rule::in(['hidden', 'visible', 'started', 'finished'])],
            'price' => 'nullable,integer|min:0',
            'tournament_start' => ['nullable', new AfterOrEqualLanStartTime($input->input('lan_id'))],
            'tournament_end' => ['nullable', 'after:tournament_start', new BeforeOrEqualLanEndTime($input->input('lan_id'))],
            'players_to_reach' => ['nullable', 'min:1', 'integer', new PlayersToReachLock($tournamentId)],
            'teams_to_reach' => 'nullable,min:1|integer',
            'rules' => 'nullable,string'
        ]);

        if ($tournamentValidator->fails()) {
            throw new BadRequestHttpException($tournamentValidator->errors());
        }

        $tournament = $this->tournamentRepository->findTournamentById($tournamentId);

        return $this->tournamentRepository->update(
            $tournament,
            $input->input('name'),
            $input->input('state'),
            new DateTime($input->input('tournament_start')),
            new DateTime($input->input('tournament_end')),
            $input->input('players_to_reach'),
            $input->input('teams_to_reach'),
            $input->input('rules'),
            intval($input->input('price'))
        );
    }
}
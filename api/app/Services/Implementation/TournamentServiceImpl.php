<?php

namespace App\Services\Implementation;


use App\Model\Tournament;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\TournamentRepositoryImpl;
use App\Rules\AfterOrEqualLanStartTime;
use App\Rules\BeforeOrEqualLanEndTime;
use App\Services\TournamentService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    // TODO Documentation
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

        return $this->tournamentRepository->create(
            $lan,
            $input->input('name'),
            new DateTime($input->input('tournament_start')),
            new DateTime($input->input('tournament_end')),
            $input->input('players_to_reach'),
            $input->input('teams_to_reach'),
            $input->input('rules'),
            intval($input->input('price'))
        );
    }
}
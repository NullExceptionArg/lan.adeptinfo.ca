<?php


namespace App\Services\Implementation;


use App\Model\Lan;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Model\Reservation;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Seatsio\SeatsioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LanServiceImpl implements LanService
{
    protected $lanRepository;

    /**
     * LanServiceImpl constructor.
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(LanRepositoryImpl $lanRepositoryImpl)
    {
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function createLan(Request $input): Lan
    {
        $lanValidator = Validator::make($input->all(), [
            'lan_start' => 'required|after:reservation_start|after:tournament_start',
            'lan_end' => 'required|after:lan_start',
            'reservation_start' => 'required|after_or_equal:now',
            'tournament_start' => 'required|after_or_equal:now',
            'event_key_id' => 'required|string|max:255',
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => 'required|string|max:255',
            'price' => 'required|integer|min:0'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        return $this->lanRepository->createLan
        (
            new DateTime($input['lan_start']),
            new DateTime($input['lan_end']),
            new DateTime($input['reservation_start']),
            new DateTime($input['tournament_start']),
            $input['event_key_id'],
            $input['public_key_id'],
            $input['secret_key_id'],
            $input['price']
        );
    }

    public function createReservation(Request $input): Reservation
    {
        $user = Auth::user();
        $lan = $this->lanRepository->findById($input['lan_id']);


        // validate data
            // user can only be once in a lan
            // seat can only be once in a lan
            // seat is a required string
            // lan is a required unsigned integer
            //

        // send the place to the api
        $seatsClient = new SeatsioClient($lan->secret_key_id);
        $seatsClient->events()->book($lan->event_key_id, [$input['seat_id']]);

        // assign place to user in lan
        $this->lanRepository->attachUserLan($user, $lan, $input['seat_id']);

        // return the reservation
        return new Reservation();
    }
}
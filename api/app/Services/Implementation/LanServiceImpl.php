<?php


namespace App\Services\Implementation;


use App\Model\Lan;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Services\LanService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Seatsio\SeatsioClient;
use Seatsio\SeatsioException;
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

        // Internal validation

        $lanValidator = Validator::make($input->all(), [
            'lan_start' => 'required|after:seat_reservation_start|after:tournament_reservation_start',
            'lan_end' => 'required|after:lan_start',
            'seat_reservation_start' => 'required|after_or_equal:now',
            'tournament_reservation_start' => 'required|after_or_equal:now',
            'event_key_id' => 'required|string|max:255',
            'public_key_id' => 'required|string|max:255',
            'secret_key_id' => 'required|string|max:255',
            'price' => 'required|integer|min:0'
        ]);

        if ($lanValidator->fails()) {
            throw new BadRequestHttpException($lanValidator->errors());
        }

        // Seats.io validation

        $seatsClient = new SeatsioClient($input['secret_key_id']);
        // Test if secret key is id valid
        try {
            $seatsClient->charts()->listAllTags();
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "secret_key_id" => [
                    'Secret key id: ' . $input['secret_key_id'] . ' is not valid.'
                ]
            ]));
        }

        // Test if event key id is valid
        try {
            $seatsClient->events()->retrieve($input['event_key_id']);
        } catch (SeatsioException $exception) {
            throw new BadRequestHttpException(json_encode([
                "event_key_id" => [
                    'Event key id: ' . $input['event_key_id'] . ' is not valid.'
                ]
            ]));
        }


        return $this->lanRepository->createLan
        (
            new DateTime($input['lan_start']),
            new DateTime($input['lan_end']),
            new DateTime($input['seat_reservation_start']),
            new DateTime($input['tournament_reservation_start']),
            $input['event_key_id'],
            $input['public_key_id'],
            $input['secret_key_id'],
            $input['price']
        );
    }
}
<?php


namespace App\Services\Implementation;

use App\Http\Resources\User\GetUserCollection;
use App\Http\Resources\User\GetUserDetailsResource;
use App\Model\User;
use App\Repositories\Implementation\LanRepositoryImpl;
use App\Repositories\Implementation\SeatRepositoryImpl;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserServiceImpl implements UserService
{
    protected $userRepository;
    protected $seatRepository;
    protected $lanRepository;

    /**
     * UserServiceImpl constructor.
     * @param UserRepositoryImpl $userRepositoryImpl
     * @param SeatRepositoryImpl $seatRepositoryImpl
     * @param LanRepositoryImpl $lanRepositoryImpl
     */
    public function __construct(
        UserRepositoryImpl $userRepositoryImpl,
        SeatRepositoryImpl $seatRepositoryImpl,
        LanRepositoryImpl $lanRepositoryImpl
    )
    {
        $this->userRepository = $userRepositoryImpl;
        $this->seatRepository = $seatRepositoryImpl;
        $this->lanRepository = $lanRepositoryImpl;
    }

    public function signUpUser(Request $request): User
    {
        $userValidator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:6|max:20'
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        return $this->userRepository->createUser(
            $request['first_name'],
            $request['last_name'],
            $request['email'],
            $request['password']
        );
    }

    public function logOut(): void
    {
        $accessToken = Auth::user()->token();

        $this->userRepository->revokeRefreshToken($accessToken);
        $this->userRepository->revokeAccessToken($accessToken);
    }

    public function deleteUser(): void
    {
        $user = Auth::user();
        $this->userRepository->deleteUserById($user->id);
    }

    public function getUsers(Request $request): GetUserCollection
    {
        $userValidator = Validator::make($request->all(), [
            'query_string' => 'max:255|string',
            'order_column' => [Rule::in(['first_name', 'last_name', 'email']),],
            'order_direction' => [Rule::in(['asc', 'desc']),],
            'items_per_page' => 'numeric|min:1|max:75',
            'current_page' => 'numeric|min:1'
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        // Default order column: last_name

        if ($request->input('order_column') == null) {
            $request['order_column'] = 'last_name';
        }

        // Default order direction: asc
        if ($request->input('order_direction') == null) {
            $request['order_direction'] = 'asc';
        }

        // Default items per page: 15
        if ($request->input('items_per_page') == null) {
            $request['items_per_page'] = 15;
        }

        // Default current page: 1
        if ($request->input('current_page') == null) {
            $request['current_page'] = 1;
        }

        return new GetUserCollection($this->userRepository->getPaginatedUsersCriteria(
            $request->input('query_string'),
            $request->input('order_column'),
            $request->input('order_direction'),
            $request->input('items_per_page'),
            $request->input('current_page')
        ));
    }

    public function getUserDetails(Request $input): GetUserDetailsResource
    {
        $userValidator = Validator::make([
            'lan_id' => $input->input('lan_id'),
            'email' => $input->input('email')
        ], [
            'lan_id' => 'integer|exists:lan,id',
            'email' => 'exists:user,email',
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        $user = $this->userRepository->findByEmail($input->input('email'));
        $lan = $this->lanRepository->findLanById($input->input('lan_id'));

        $currentSeat = $this->seatRepository->getCurrentSeat($user, $lan);
        $seatHistory = $this->seatRepository->getSeatHistoryForUser($user, $lan);

        return new GetUserDetailsResource($user, $currentSeat, $seatHistory);
    }
}
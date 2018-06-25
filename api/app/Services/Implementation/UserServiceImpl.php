<?php


namespace App\Services\Implementation;

use App\Http\Resources\User\GetUserCollection;
use App\Model\User;
use App\Repositories\Implementation\UserRepositoryImpl;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserServiceImpl implements UserService
{
    protected $userRepository;

    /**
     * UserServiceImpl constructor.
     * @param $userRepositoryImpl
     */
    public function __construct(UserRepositoryImpl $userRepositoryImpl)
    {
        $this->userRepository = $userRepositoryImpl;
    }

    public function signUpUser(Request $input): User
    {
        $userValidator = Validator::make($input->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:user',
            'password' => 'required|min:6|max:20'
        ]);

        if ($userValidator->fails()) {
            throw new BadRequestHttpException($userValidator->errors());
        }

        return $this->userRepository->createUser(
            $input['first_name'],
            $input['last_name'],
            $input['email'],
            $input['password']
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
        return new GetUserCollection($this->userRepository->getPaginatedUsersCriteria(
            $request->input('query_string'),
            $request->input('order_column'),
            $request->input('order_direction'),
            $request->input('items_per_page'),
            $request->input('current_page')
        ));
    }
}
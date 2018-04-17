<?php


namespace App\Services\Implementation;


use App\Model\User;
use App\Repositories\UserRepositoryImpl;
use App\Services\UserService;
use Illuminate\Http\Request;
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


    public function signUp(Request $input): User
    {
        $userValidator = Validator::make($input->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|unique:users|max:255',
        ]);

        if($userValidator->fails()){
            throw new BadRequestHttpException($userValidator->errors());
        }

        return $this->userRepository->createUser(
            $input['first_name'],
            $input['last_name'],
            $input['email'],
            $input['password']
        );
    }
}
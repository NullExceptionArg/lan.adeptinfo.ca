<?php

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserServiceTest extends TestCase
{
    protected $userService;

    use DatabaseMigrations;

    protected $paramsContent = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@doe.com',
        'password' => 'Passw0rd!'
    ];

    public function setUp()
    {
        parent::setUp();
        $this->userService = $this->app->make('App\Services\Implementation\UserServiceImpl');
    }

    public function testSignUp()
    {
        // Default
        $request = new Request($this->paramsContent);
        $result = $this->userService->signUp($request);

        $this->assertEquals($this->paramsContent['first_name'], $result->first_name);
        $this->assertEquals($this->paramsContent['last_name'], $result->last_name);
        $this->assertEquals($this->paramsContent['email'], $result->email);
    }

    public function testSignUpUniqueEmailConstraints()
    {
        // Required
        $this->paramsContent['email'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('Expected: {"email":["The email field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email field is required."]}', $e->getMessage());
        }


        // Formatted email
        $this->paramsContent['email'] = 'john.doe.com';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"email":["The email must be a valid email address."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email must be a valid email address."]}', $e->getMessage());
        }

        // Unique email
        $this->paramsContent['email'] = 'john@doe.com';
        $user = new User();
        $user->first_name = $this->paramsContent['first_name'];
        $user->last_name = $this->paramsContent['last_name'];
        $user->email = $this->paramsContent['email'];
        $user->password = Hash::make($this->paramsContent['password']);
        $user->save();
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"email":["The email has already been taken."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"email":["The email has already been taken."]}', $e->getMessage());
        }
    }

    public function testSignUpPasswordConstraints()
    {
        // Required
        $this->paramsContent['password'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"password":["The password field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"password":["The password field is required."]}', $e->getMessage());
        }

        // Min Length
        $this->paramsContent['password'] = str_repeat('☭', 2);
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"password":["The password must be at least 6 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"password":["The password must be at least 6 characters."]}', $e->getMessage());
        }

        // Max Length
        $this->paramsContent['password'] = str_repeat('☭', 22);
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"password":["The password may not be greater than 20 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"password":["The password may not be greater than 20 characters."]}', $e->getMessage());
        }
    }

    public function testSignUpFirstNameConstraints()
    {
        // Required
        $this->paramsContent['first_name'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"first_name":["The first name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"first_name":["The first name field is required."]}', $e->getMessage());
        }

        // Max Length
        $this->paramsContent['first_name'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"first_name":["The first name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"first_name":["The first name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }

    public function testSignUpLastNameConstraints()
    {
        // Required
        $this->paramsContent['last_name'] = '';
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"last_name":["The last name field is required."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"last_name":["The last name field is required."]}', $e->getMessage());
        }

        // Max Length
        $this->paramsContent['last_name'] = str_repeat('☭', 256);
        $request = new Request($this->paramsContent);
        try {
            $this->userService->signUp($request);
            $this->fail('{"last_name":["The last name may not be greater than 255 characters."]}');
        } catch (BadRequestHttpException $e) {
            $this->assertEquals(400, $e->getStatusCode());
            $this->assertEquals('{"last_name":["The last name may not be greater than 255 characters."]}', $e->getMessage());
        }
    }
}

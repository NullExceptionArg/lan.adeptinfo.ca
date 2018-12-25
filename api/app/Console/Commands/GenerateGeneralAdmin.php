<?php

namespace App\Console\Commands;

use App\Model\GlobalRole;
use App\Rules\UniqueEmailSocialLogin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class GenerateGeneralAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lan:general-admin {email?} {first-name?} {last-name?} {password?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the general user of the LAN. This is the user required to promote the first other users and create the first LAN.';

    public function handle()
    {

        $this->comment('Generating general administrator');

        $email = $this->argument('email');
        $firstName = $this->argument('first-name');
        $lastName = $this->argument('last-name');
        $password = $this->argument('password');

        if (is_null($email)) {
            $lanValidator = null;
            do {
                if (!is_null($lanValidator) && $lanValidator->fails()) {
                    $this->warn('Invalid email. Please try again.');
                }
                $email = $this->ask('Email (valid email, to used in a social login) : ');
                $lanValidator = Validator::make(['email' => $email,], ['email' => ['required', 'email', new UniqueEmailSocialLogin]]);
            } while ($lanValidator->fails());
        }


        if (is_null($firstName)) {
            $firstNameValidator = null;
            do {
                if (!is_null($firstNameValidator) && $firstNameValidator->fails()) {
                    $this->warn('Invalid first name. Please try again.');
                }

                $firstName = $this->ask('First name (255 character max) : ');
                $firstNameValidator = Validator::make(['first_name' => $firstName,], ['first_name' => 'required|max:255']);
            } while ($firstNameValidator->fails());
        }

        if (is_null($lastName)) {
            $lastNameValidator = null;
            do {
                if (!is_null($lastNameValidator) && $lastNameValidator->fails()) {
                    $this->warn('Invalid last name. Please try again. (255 character max)');
                }
                $lastName = $this->ask('Last name (255 character max) : ');
                $lastNameValidator = Validator::make(['last_name' => $lastName,], ['last_name' => 'required|max:255']);
            } while ($lastNameValidator->fails());
        }

        if (is_null($password)) {
            $passwordValidator = null;
            $samePasswords = true;
            do {
                if (!is_null($passwordValidator) && ($passwordValidator->fails() || !$samePasswords)) {
                    $this->warn('Invalid password name. Please try again.');
                }
                $password = $this->secret('Password (6 character min, 20 character max) : ');
                $passwordValidator = Validator::make(['password' => $password,], ['password' => 'required|min:6|max:20']);

                $passwordConfirmation = $this->secret('Confirm password');
                $samePasswords = $password == $passwordConfirmation;
            } while ($passwordValidator->fails() || !$samePasswords);
        }

        $userValidator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => $password
        ], [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => ['required', 'email', new UniqueEmailSocialLogin],
            'password' => 'required|min:6|max:20'
        ]);

        if ($userValidator->fails()) {
            $this->error('Invalid input');
            exit();
        }

        $userId = DB::table('user')->insertGetId([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'password' => Hash::make($password),
            'is_confirmed' => true
        ]);

        DB::table('global_role_user')->insert([
            'role_id' => GlobalRole::where('name', 'general-admin')->first()->id,
            'user_id' => $userId
        ]);

        $this->info('General administrator generated');
        $headers = ['email', 'first name', 'last name'];
        $user = json_decode(json_encode(DB::table('user')->where('email', $email)->get(['email', 'first_name', 'last_name'])), true);
        $this->table($headers, $user);
        $this->line('');
    }
}

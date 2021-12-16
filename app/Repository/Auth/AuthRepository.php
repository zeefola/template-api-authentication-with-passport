<?php

namespace App\Repository\Auth;

use App\Events\Users\UserActivated;
use App\Events\Users\UserRegistered;
use App\Repository\Actors\UserActor;
use App\Traits\Tracking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

/**
 * Class AuthRepository
 * @package App\Repository\User
 */
class AuthRepository
{
    use Tracking;

    /**
     * @var UserActor
     */
    private $user;
    /**
     * AuthRepository constructor.
     * @param UserActor $user
     */
    public function __construct(UserActor $user)
    {
        $this->user = $user;
    }
    /**
     * Register new user
     * @param $input
     * @return array []
     * @throws Exception
     */
    public function registerUser($input): array
    {
        $name = $input['name'];
        $phone_number = $input['phone_number'];
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $password = $input['password'];

        $emailExists = $this->user->where('email', $email)
            ->first();

        if ($emailExists) {
            return [
                'error' => true,
                'msg' => [
                    'email' => array(['Email associated with another account.'])
                ]
            ];
        }

        $phoneNumberExists = $this->user->where('phone_number', $phone_number)
            ->first();

        if ($phoneNumberExists) {
            return [
                'error' => true,
                'msg' => [
                    'phone_number' => array(['Phone number associated with another account.'])
                ]
            ];
        }

        $uuid = "";
        try {
            $uuid = Uuid::uuid4();
        } catch (UnableToBuildUuidException $e) {
        }

        $id = $uuid->toString();
        $confirmation_code = Str::random(20);
        $confirm = rand(111111, 999999);

        $this->user->create([
            'user_id' => $id,
            'name' => $name,
            'phone_number' => $phone_number,
            'email' => $email,
            'password' => bcrypt($password),
            'remember_token' => $confirmation_code,
            'activation_created' => Carbon::now(),
            'confirm_code' => $confirm,
            'created_at' => Carbon::now()
        ]);

        $url = config('app.frontend_url') . '/confirm-account?email=' . $email . '&token=' . $confirmation_code;
        $user = $this->user->findBy('user_id', $id);

        event(new UserRegistered($user, [
            'title' => 'Confirm Account',
            'name' => $name,
            'url' => $url,
            'email' => $email,
            'confirm_code' => $confirm
        ]));

        return [
            'error' => false,
            'msg' => 'Registration successful. Check your email for confirmation mail',
            'data' => $user
        ];
    }

    /**
     * Confirm user registration
     * @param $email
     * @param $token
     * @return void
     */
    public function confirmAccount($email, $token)
    {
        $user = $this->user->findBy('email', $email);

        if (!empty($user)) {
            $since = Carbon::parse($user->activation_created)->diffInHours(Carbon::now());
            if ($since <= 24) {
                if (!$user->active) {
                    if ($user->remember_token === $token) {
                        $user->active = '1';
                        $user->remember_token = '';
                        $user->confirm_code = '';
                        $user->email_verified_at = Carbon::now();

                        $user->save();

                        event(new UserActivated($user, [
                            'title' => 'Welcome to Kusnap',
                            'name' => $user->name
                        ]));

                        header("Location: " . config('app.frontend_url') . "/login?error=false&response=account+has+been+activated+successfully");
                        die();
                    }
                    header("Location: " . config('app.frontend_url') . "/login?error=false&response=invalid+activation+link");
                    die();
                }
                header("Location: " . config('app.frontend_url') . "/login?error=false&response=account+has+already+been+verified");
                die();
            }
            header("Location: " . config('app.frontend_url') . "/login?error=false&response=activation+link+has+expired");
            die();
        }
        header("Location: " . config('app.frontend_url') . "/login?error=false&response=account+does+not+exist");
        die();
    }
    /**
     * Resend user confirmation code
     * @param $input
     * @return array []
     */
    public function resendCode($input): array
    {
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $user = $this->user->findBy('email', $email);

        if (!empty($user)) {
            $confirmation_code = Str::random(20);
            $confirm = rand(111111, 999999);

            $user->remember_token = $confirmation_code;
            $user->confirm_code = $confirm;
            $user->activation_created = Carbon::now();
            $user->save();

            $url = config('app.frontend_url') . '/confirm-account?email=' . $email . '&token=' . $confirmation_code;

            event(new UserRegistered($user, [
                'title' => 'Confirm Account',
                'name' => $user->name,
                'url' => $url,
                'email' => $email,
                'confirm_code' => $confirm
            ]));

            return [
                'msg' => 'Account activation code has been sent.',
                'error' => false
            ];
        }
        return [
            'msg' => 'Account does not exist',
            'error' => true
        ];
    }
}
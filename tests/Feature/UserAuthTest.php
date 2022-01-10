<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;

use App\Models\User;

use App\Events\Users\UserRegistered;
use App\Events\Users\UserActivated;
use App\Events\Users\UserChangePassword;

class UserAuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Test registration validation error
     *
     * @return void
     */
    public function testRegisterationValidationError()
    {
        $response = $this->json('POST', '/api/register', []);

        $response->assertJson([
            "error" => true,
            "msg" => [
                "name" => [
                    "The name field is required."
                ],
                "phone_number" => [
                    "The phone number field is required."
                ],
                "email" => [
                    "The email field is required."
                ],
                "password" => [
                    "The password field is required."
                ]
            ]
        ]);
    }

    /**
     * Test registration
     *
     * @return void
     */
    public function testRegistration()
    {
        Event::fake();

        $name = $this->faker->name();
        $phone_number = '081' . (string)$this->faker->numerify("########");
        $email = $this->faker->unique()->safeEmail;
        $password = $this->faker->password(8);

        $response = $this->json('POST', '/api/register', [
            "name" => $name,
            'phone_number' => $phone_number,
            'email' => $email,
            'password' => $password
        ]);

        Event::assertDispatched(UserRegistered::class);

        $response->assertJson([
            "error" => false,
            "msg" => "Registration successful. Check your email for confirmation mail"
        ]);

        $this->assertDatabaseHas('users', [
            'name' => $name,
            'phone_number' => $phone_number,
            'email' => $email,
            'password' => $password
        ]);
    }

    /**
     * Test confirm account using confirmation token
     *
     * @return void
     */
    public function testConfirmAccount()
    {
        Event::fake();

        $user = User::factory()->create();
        // echo $user->email;
        $response = $this->json('POST', '/api/confirm-account', [
            'email' => $user->email,
            'confirm_code' => $user->remember_token
        ]);

        Event::assertDispatched(UserActivated::class);

        $response->assertJson([
            "error" => false,
            "msg" => "Account has been activated successfully."
        ]);
    }

    /**
     * Test confirm account using mobile code
     *
     * @return void
     */
    public function testConfirmCode()
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this->json('POST', '/api/confirm-mobile-code', [
            'phone_number' => $user->phone_number,
            'confirm_code' => $user->confirm_code
        ]);

        Event::assertDispatched(UserActivated::class);

        $response->assertJson([
            "error" => false,
            "msg" => "Account has been activated successfully."
        ]);
    }

    /**
     * Test resend confirmation code
     *
     * @return void
     */
    public function testResendConfirmationCode()
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this->json('POST', '/api/resend-confirmation-code', [
            'email' => $user->email
        ]);

        Event::assertDispatched(UserRegistered::class);

        $response->assertJson([
            "error" => false,
            "msg" => "Account activation code has been sent."
        ]);
    }

    /**
     * Test login error
     *
     * @return void
     */
    public function testLoginValidationErrors()
    {
        $response = $this->json('POST', '/api/login', []);

        $response->assertJson([
            "error" => true,
            "msg" => [
                "email" => [
                    "The email field is required."
                ],
                "password" => [
                    "The password field is required."
                ]
            ]
        ]);
    }

    /**
     * Test account not activated
     *
     * @return void
     */
    public function testAccountDoesNotExistError()
    {
        User::factory()->create();
        $email = $this->faker->email();

        $response = $this->json('POST', '/api/login', [
            "email" => $email,
            "password" => "secret"
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Account does not exist."
        ]);
    }

    /**
     * Test account not activated
     *
     * @return void
     */
    public function testAccountNotActivatedError()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "secret"
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Account has not been activated. Check your mail for activation link."
        ]);
    }

     /**
     * Test login
     *
     * @return void
     */
    public function testLogin()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => ""
        ]);

        Passport::actingAs($user);

        $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "password"
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test login attempt
     *
     * @return void
     */
    public function testLoginAttempt()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => ""
        ]);

        Passport::actingAs($user);

        $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "password123"
        ]);

        $this->assertDatabaseHas('users', [
            "user_id" => $user->user_id,
            "attempts" => 1
        ]);
    }

    /**
     * Test login attempt blocking
     *
     * @return void
     */
    public function testLoginAttemptBlocking()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => "",
            "attempts" => 3,
            "last_attempt_limit" => Carbon::now()
        ]);

        Passport::actingAs($user);

        $response = $this->json('POST', '/api/login', [
            "email" => $user->email,
            "password" => "password123"
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Login attempt limit exceeded. Try again in an hour."
        ]);
    }

    /**
     * Test reset password, email does not exist
     *
     * @return void
     */
    public function testResetPasswordAccountDoesNotExistError()
    {
        User::factory()->create();
        $email = $this->faker->email();

        $response = $this->json('POST', '/api/reset-password', [
            "email" => $email
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Email is not associated with any account."
        ]);
    }

    /**
     * Test reset password
     *
     * @return void
     */
    public function testResetPassword()
    {
        Event::fake();

        $user = User::factory()->create();

        $response = $this->json('POST', '/api/reset-password', [
            "email" => $user->email
        ]);

        Event::assertDispatched(UserChangePassword::class);

        $response->assertJson([
            "error" => false,
            "msg" => "A new password has been sent to your email"
        ]);
    }

    /**
     * Test change password, confirm password
     *
     * @return void
     */
    public function testChangePasswordConfirmationError()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => ""
        ]);

        Passport::actingAs($user);

        $password = $this->faker->password(8);
        $confirmpassword = $this->faker->password(9);

        $response = $this->json('POST', '/api/update-password', [
            "oldpassword" => "password",
            "password" => $password,
            "confirmpassword" => $confirmpassword
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Incorrect confirm password"
        ]);
    }

    /**
     * Test change password, incorrect old password
     *
     * @return void
     */
    public function testChangePasswordIncorrectOldPasswordError()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => ""
        ]);

        Passport::actingAs($user);

        $password = $this->faker->password(8);

        $response = $this->json('POST', '/api/update-password', [
            "oldpassword" => "password123",
            "password" => $password,
            "confirmpassword" => $password
        ]);

        $response->assertJson([
            "error" => true,
            "msg" => "Old password is incorrect"
        ]);
    }

    /**
     * Test change password
     *
     * @return void
     */
    public function testChangePassword()
    {
        $user = User::factory()->create([
            "active" => true,
            "remember_token" => ""
        ]);

        Passport::actingAs($user);

        $password = $this->faker->password(8);

        $response = $this->json('POST', '/api/update-password', [
            "oldpassword" => "password",
            "password" => $password,
            "confirmpassword" => $password
        ]);

        $response->assertJson([
            "error" => false,
            "msg" => "Password changed successfully"
        ]);
    }
}

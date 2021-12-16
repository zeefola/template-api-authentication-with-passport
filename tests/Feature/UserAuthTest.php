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
}
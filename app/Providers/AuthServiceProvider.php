<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Passport;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (App::environment('local')) {
            Passport::loadKeysFrom(__DIR__ . '/../../auth-keys/local');
        }
        if (App::environment('development')) {
            Passport::loadKeysFrom(__DIR__ . '/../../auth-keys/development');
        }
        if (App::environment('staging')) {
            Passport::loadKeysFrom(__DIR__ . '/../../auth-keys/staging');
        }
        if (App::environment('production')) {
            Passport::loadKeysFrom(__DIR__ . '/../../auth-keys/production');
        }
    }
}
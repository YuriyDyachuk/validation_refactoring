<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use Illuminate\Support\ServiceProvider;
use App\Services\Auth\Strategies\EmailStrategy;
use App\Services\Auth\Strategies\GoogleStrategy;
use App\Services\Auth\Strategies\PhoneStrategy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService([
                'email' => new EmailStrategy(),
                'phone' => new PhoneStrategy(),
                'google' => new GoogleStrategy(),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

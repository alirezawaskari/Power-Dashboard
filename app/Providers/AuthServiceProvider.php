<?php

namespace App\Providers;

use App\Services\Auth\JwtVerifier;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(JwtVerifier::class, function ($app) {
            // For testing, use mock values
            if ($app->environment('testing')) {
                return new JwtVerifier(
                    issuer: 'test-issuer',
                    audience: 'test-audience',
                    jwksUri: 'https://test.example.com/.well-known/jwks.json',
                    jwksTtl: 300
                );
            }

            return JwtVerifier::fromConfig();
        });
    }
}
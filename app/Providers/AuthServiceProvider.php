<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
    \App\Models\Video::class => \App\Policies\VideoPolicy::class,
    \App\Models\QuizAttempt::class => \App\Policies\QuizAttemptPolicy::class,
];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

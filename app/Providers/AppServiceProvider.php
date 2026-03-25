<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Repositories\UserRepository::class);
        $this->app->singleton(\App\Repositories\DonationRepository::class);
        $this->app->singleton(\App\Repositories\DonationRequestRepository::class);
        $this->app->singleton(\App\Repositories\DonationAssignmentRepository::class);
        $this->app->singleton(\App\Repositories\RatingRepository::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}

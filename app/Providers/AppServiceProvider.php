<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;
use Stripe\HttpClient\CurlClient;
use Stripe\ApiRequestor;
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Repositories\UserRepository::class);
        $this->app->singleton(\App\Repositories\DonationRepository::class);
        $this->app->singleton(\App\Repositories\DonationRequestRepository::class);
        $this->app->singleton(\App\Repositories\DonationAssignmentRepository::class);
        $this->app->singleton(\App\Services\SubscriptionPaymentService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrapFive();
        if (app()->environment('local')) {

          Stripe::setVerifySslCerts(false);
  
          $curl = new CurlClient();
          $curl->setEnableHttp2(false);
  
          ApiRequestor::setHttpClient($curl);
      }
    }
}

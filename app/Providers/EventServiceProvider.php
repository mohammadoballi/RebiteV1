<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\DonationStatusChanged;
use App\Events\NewDonationRequest;
use App\Events\VolunteerAssigned;
use App\Listeners\SendDonationNotification;
use App\Listeners\SendRequestNotification;
use App\Listeners\SendAssignmentNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        DonationStatusChanged::class => [
            SendDonationNotification::class,
        ],
        NewDonationRequest::class => [
            SendRequestNotification::class,
        ],
        VolunteerAssigned::class => [
            SendAssignmentNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

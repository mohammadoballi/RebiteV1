<?php

namespace App\Listeners;

use App\Events\NewDonationRequest;
use App\Services\NotificationService;

class SendRequestNotification
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(NewDonationRequest $event): void
    {
        $this->notificationService->notifyNewDonationRequest($event->donationRequest);
    }
}

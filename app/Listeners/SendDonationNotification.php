<?php

namespace App\Listeners;

use App\Events\DonationStatusChanged;
use App\Services\NotificationService;

class SendDonationNotification
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(DonationStatusChanged $event): void
    {
        $this->notificationService->notifyDonationStatusChanged($event->donation);
    }
}

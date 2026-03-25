<?php

namespace App\Listeners;

use App\Events\VolunteerAssigned;
use App\Services\NotificationService;

class SendAssignmentNotification
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(VolunteerAssigned $event): void
    {
        $this->notificationService->notifyAssignment($event->assignment);
    }
}

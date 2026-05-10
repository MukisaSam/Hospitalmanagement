<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Services\AuditLogService;

class AppointmentObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(Appointment $appointment): void
    {
        $this->auditLogService->log('created', $appointment, [], $appointment->getAttributes());
    }

    public function updated(Appointment $appointment): void
    {
        $this->auditLogService->log('updated', $appointment, $appointment->getOriginal(), $appointment->getChanges());
    }

    public function deleted(Appointment $appointment): void
    {
        $this->auditLogService->log('deleted', $appointment, $appointment->getAttributes(), []);
    }
}

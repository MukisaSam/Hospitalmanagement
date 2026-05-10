<?php

namespace App\Observers;

use App\Models\Doctor;
use App\Services\AuditLogService;

class DoctorObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(Doctor $doctor): void
    {
        $this->auditLogService->log('created', $doctor, [], $doctor->getAttributes());
    }

    public function updated(Doctor $doctor): void
    {
        $this->auditLogService->log('updated', $doctor, $doctor->getOriginal(), $doctor->getChanges());
    }

    public function deleted(Doctor $doctor): void
    {
        $this->auditLogService->log('deleted', $doctor, $doctor->getAttributes(), []);
    }

    public function restored(Doctor $doctor): void
    {
        $this->auditLogService->log('restored', $doctor, [], $doctor->getAttributes());
    }
}

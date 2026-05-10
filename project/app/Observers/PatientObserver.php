<?php

namespace App\Observers;

use App\Models\Patient;
use App\Services\AuditLogService;

class PatientObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(Patient $patient): void
    {
        $this->auditLogService->log('created', $patient, [], $patient->getAttributes());
    }

    public function updated(Patient $patient): void
    {
        $this->auditLogService->log('updated', $patient, $patient->getOriginal(), $patient->getChanges());
    }

    public function deleted(Patient $patient): void
    {
        $this->auditLogService->log('deleted', $patient, $patient->getAttributes(), []);
    }

    public function restored(Patient $patient): void
    {
        $this->auditLogService->log('restored', $patient, [], $patient->getAttributes());
    }
}

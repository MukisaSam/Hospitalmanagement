<?php

namespace App\Observers;

use App\Models\MedicalRecord;
use App\Services\AuditLogService;

class MedicalRecordObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(MedicalRecord $medicalRecord): void
    {
        $this->auditLogService->log('created', $medicalRecord, [], $medicalRecord->getAttributes());
    }

    public function updated(MedicalRecord $medicalRecord): void
    {
        $this->auditLogService->log('updated', $medicalRecord, $medicalRecord->getOriginal(), $medicalRecord->getChanges());
    }

    public function deleted(MedicalRecord $medicalRecord): void
    {
        $this->auditLogService->log('deleted', $medicalRecord, $medicalRecord->getAttributes(), []);
    }
}

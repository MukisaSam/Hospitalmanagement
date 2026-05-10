<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\AuditLogService;

class PaymentObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(Payment $payment): void
    {
        $this->auditLogService->log('created', $payment, [], $payment->getAttributes());
    }

    public function updated(Payment $payment): void
    {
        $this->auditLogService->log('updated', $payment, $payment->getOriginal(), $payment->getChanges());
    }

    public function deleted(Payment $payment): void
    {
        $this->auditLogService->log('deleted', $payment, $payment->getAttributes(), []);
    }
}

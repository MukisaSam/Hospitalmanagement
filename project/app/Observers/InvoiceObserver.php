<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\AuditLogService;

class InvoiceObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(Invoice $invoice): void
    {
        $this->auditLogService->log('created', $invoice, [], $invoice->getAttributes());
    }

    public function updated(Invoice $invoice): void
    {
        $this->auditLogService->log('updated', $invoice, $invoice->getOriginal(), $invoice->getChanges());
    }

    public function deleted(Invoice $invoice): void
    {
        $this->auditLogService->log('deleted', $invoice, $invoice->getAttributes(), []);
    }
}

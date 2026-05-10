<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogService;

class UserObserver
{
    public function __construct(private AuditLogService $auditLogService) {}

    public function created(User $user): void
    {
        $this->auditLogService->log('created', $user, [], $user->getAttributes());
    }

    public function updated(User $user): void
    {
        $this->auditLogService->log('updated', $user, $user->getOriginal(), $user->getChanges());
    }

    public function deleted(User $user): void
    {
        $this->auditLogService->log('deleted', $user, $user->getAttributes(), []);
    }

    public function restored(User $user): void
    {
        $this->auditLogService->log('restored', $user, [], $user->getAttributes());
    }
}

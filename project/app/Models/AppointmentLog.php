<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentLog extends Model
{
    protected $fillable = [
        'appointment_id',
        'old_status',
        'new_status',
        'old_date',
        'new_date',
        'old_time',
        'new_time',
        'changed_by',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'old_date' => 'date',
            'new_date' => 'date',
        ];
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}

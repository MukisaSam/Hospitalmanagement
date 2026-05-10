<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vital extends Model
{
    protected $fillable = [
        'medical_record_id',
        'blood_pressure',
        'pulse_rate',
        'temperature',
        'weight',
        'height',
        'bmi',
        'oxygen_saturation',
        'recorded_by',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'temperature'       => 'decimal:1',
            'weight'            => 'decimal:2',
            'height'            => 'decimal:2',
            'bmi'               => 'decimal:2',
            'oxygen_saturation' => 'decimal:1',
            'recorded_at'       => 'datetime',
        ];
    }

    public function medicalRecord(): BelongsTo
    {
        return $this->belongsTo(MedicalRecord::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

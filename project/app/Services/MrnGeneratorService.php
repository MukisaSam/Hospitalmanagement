<?php

namespace App\Services;

use App\Models\Patient;

class MrnGeneratorService
{
    public function generate(): string
    {
        $prefix = 'MRN-' . now()->format('Ymd') . '-';
        $last = Patient::withTrashed()
            ->where('mrn', 'like', $prefix . '%')
            ->orderBy('mrn', 'desc')
            ->value('mrn');
        $sequence = $last ? (int) substr($last, -4) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount_paid',
        'payment_method',
        'reference_number',
        'payment_date',
        'paid_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount_paid'    => 'decimal:2',
            'payment_method' => PaymentMethod::class,
            'payment_date'   => 'date',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}

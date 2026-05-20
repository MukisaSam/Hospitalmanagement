<?php

namespace App\Http\Requests;

use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount_paid'      => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,card,mobile_money,bank_transfer,insurance',
            'reference_number' => 'nullable|string|max:100',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $invoice = $this->route('invoice');
            if (!$invoice) {
                return;
            }

            if (in_array($invoice->status?->value, ['paid', 'cancelled'], true)) {
                $validator->errors()->add('amount_paid', 'Payment cannot be recorded for this invoice.');
                return;
            }

            $pendingExists = $invoice->payments()
                ->where('status', PaymentStatus::Pending)
                ->exists();

            if ($pendingExists) {
                $validator->errors()->add('amount_paid', 'A payment is already pending confirmation.');
                return;
            }

            $outstanding = max(0, (float) $invoice->total_amount - (float) $invoice->amount_paid);
            $amount = round((float) $this->input('amount_paid'), 2);

            if ($outstanding <= 0) {
                $validator->errors()->add('amount_paid', 'This invoice is already paid.');
                return;
            }

            if ($amount !== round($outstanding, 2)) {
                $validator->errors()->add('amount_paid', 'Payment must cover the full outstanding balance.');
            }
        });
    }
}

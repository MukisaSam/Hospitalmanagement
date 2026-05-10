<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private InvoiceNumberService $invoiceNumberService,
        private SettingService $settingService
    ) {}

    public function createForAppointment(Appointment $appointment): Invoice
    {
        // Idempotent: return existing invoice if already created for this appointment
        if (Invoice::where('appointment_id', $appointment->id)->exists()) {
            return Invoice::where('appointment_id', $appointment->id)->first();
        }

        $consultationFee = $appointment->doctor->consultation_fee ?? 0;
        $taxRate = (float) $this->settingService->get('tax_rate', 0);
        $subtotal = $consultationFee;
        $taxAmount = round($subtotal * $taxRate / 100, 2);
        $total = $subtotal + $taxAmount;

        $invoice = Invoice::create([
            'invoice_number'  => $this->invoiceNumberService->generate(),
            'patient_id'      => $appointment->patient_id,
            'appointment_id'  => $appointment->id,
            'doctor_id'       => $appointment->doctor_id,
            'subtotal'        => $subtotal,
            'tax_rate'        => $taxRate,
            'tax_amount'      => $taxAmount,
            'discount_amount' => 0,
            'total_amount'    => $total,
            'amount_paid'     => 0,
            'status'          => 'unpaid',
            'due_date'        => now()->addDays(30)->toDateString(),
            'created_by'      => auth()->id(),
        ]);

        InvoiceItem::create([
            'invoice_id'  => $invoice->id,
            'description' => 'Consultation Fee - Dr. ' . $appointment->doctor->full_name,
            'quantity'    => 1,
            'unit_price'  => $consultationFee,
            'total_price' => $consultationFee,
        ]);

        return $invoice;
    }

    public function recordPayment(Invoice $invoice, array $data): void
    {
        DB::transaction(function () use ($invoice, $data) {
            Payment::create([
                'invoice_id'       => $invoice->id,
                'amount_paid'      => $data['amount_paid'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'payment_date'     => $data['payment_date'],
                'paid_by'          => auth()->id(),
                'notes'            => $data['notes'] ?? null,
            ]);

            $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount_paid');

            $status = 'unpaid';
            if ($totalPaid >= $invoice->total_amount) {
                $status = 'paid';
            } elseif ($totalPaid > 0) {
                $status = 'partial';
            }

            $invoice->update(['amount_paid' => $totalPaid, 'status' => $status]);
        });
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConfirmPaymentRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function store(StorePaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $this->invoiceService->recordPayment($invoice, $request->validated());
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded and queued for confirmation.');
    }

    public function confirm(ConfirmPaymentRequest $request, Invoice $invoice, Payment $payment): RedirectResponse
    {
        try {
            $this->invoiceService->confirmPayment($invoice, $payment);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment confirmed successfully.');
    }
}

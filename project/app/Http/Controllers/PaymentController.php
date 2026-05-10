<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private InvoiceService $invoiceService) {}

    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $unpayableStatuses = ['paid', 'cancelled'];
        if (in_array($invoice->status->value, $unpayableStatuses)) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Payment cannot be recorded for this invoice.');
        }

        $data = $request->validate([
            'amount_paid'      => 'required|numeric|min:0.01',
            'payment_method'   => 'required|in:cash,card,mobile_money,bank_transfer,insurance',
            'reference_number' => 'nullable|string|max:100',
            'payment_date'     => 'required|date',
            'notes'            => 'nullable|string',
        ]);

        $this->invoiceService->recordPayment($invoice, $data);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment recorded successfully.');
    }
}

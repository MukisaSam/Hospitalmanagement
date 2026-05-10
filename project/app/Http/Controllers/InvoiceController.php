<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['patient', 'doctor']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($patientId = $request->input('patient_id')) {
            $query->where('patient_id', $patientId);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();
        $patients = Patient::orderBy('first_name')->get();

        return view('invoices.index', compact('invoices', 'patients'));
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['patient', 'doctor', 'appointment', 'items', 'payments', 'createdBy']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $editableStatuses = ['unpaid', 'partial'];
        if (!in_array($invoice->status->value, $editableStatuses)) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice cannot be edited in its current status.');
        }

        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $editableStatuses = ['unpaid', 'partial'];
        if (!in_array($invoice->status->value, $editableStatuses)) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'This invoice cannot be edited in its current status.');
        }

        $data = $request->validate([
            'due_date' => 'nullable|date',
            'notes'    => 'nullable|string',
        ]);

        $invoice->update($data);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function pdf(Invoice $invoice): Response
    {
        $invoice->load(['patient', 'doctor', 'appointment', 'items', 'payments']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->download("invoice-{$invoice->invoice_number}.pdf");
    }
}

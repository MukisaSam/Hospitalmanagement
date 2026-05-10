<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceItemController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function create(Invoice $invoice): View
    {
        $this->assertEditable($invoice);

        return view('invoices.items.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice): RedirectResponse
    {
        $this->assertEditable($invoice);

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'quantity'    => 'required|numeric|min:0.01',
            'unit_price'  => 'required|numeric|min:0',
        ]);

        $data['total_price'] = round($data['quantity'] * $data['unit_price'], 2);
        $data['invoice_id']  = $invoice->id;

        InvoiceItem::create($data);

        $this->recomputeInvoiceTotals($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice item added successfully.');
    }

    public function edit(Invoice $invoice, InvoiceItem $item): View
    {
        $this->assertEditable($invoice);

        return view('invoices.items.edit', compact('invoice', 'item'));
    }

    public function update(Request $request, Invoice $invoice, InvoiceItem $item): RedirectResponse
    {
        $this->assertEditable($invoice);

        $data = $request->validate([
            'description' => 'required|string|max:255',
            'quantity'    => 'required|numeric|min:0.01',
            'unit_price'  => 'required|numeric|min:0',
        ]);

        $data['total_price'] = round($data['quantity'] * $data['unit_price'], 2);
        $item->update($data);

        $this->recomputeInvoiceTotals($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice item updated successfully.');
    }

    public function destroy(Invoice $invoice, InvoiceItem $item): RedirectResponse
    {
        $this->assertEditable($invoice);

        $item->delete();

        $this->recomputeInvoiceTotals($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice item removed successfully.');
    }

    private function assertEditable(Invoice $invoice): void
    {
        $editableStatuses = ['unpaid', 'partial'];
        abort_unless(in_array($invoice->status->value, $editableStatuses), 403, 'This invoice cannot be modified.');
    }

    private function recomputeInvoiceTotals(Invoice $invoice): void
    {
        $invoice->refresh();
        $subtotal        = $invoice->items()->sum('total_price');
        $taxRate         = (float) $this->settingService->get('tax_rate', $invoice->tax_rate ?? 0);
        $taxAmount       = round($subtotal * $taxRate / 100, 2);
        $discountAmount  = $invoice->discount_amount ?? 0;
        $total           = $subtotal + $taxAmount - $discountAmount;

        $invoice->update([
            'subtotal'     => $subtotal,
            'tax_rate'     => $taxRate,
            'tax_amount'   => $taxAmount,
            'total_amount' => max(0, $total),
        ]);
    }
}

@extends('layouts.app')

@section('title', 'Invoice ' . $invoice->invoice_number)
@section('page-title', 'Invoice Details')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Invoices', 'url' => route('invoices.index')],
    ['label' => $invoice->invoice_number],
]" />

@php $editable = in_array($invoice->status->value, ['unpaid', 'partial']); @endphp

<div class="d-flex gap-2 mb-4">
    @if($editable)
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-sm btn-outline-warning">
        <i class="fas fa-edit me-1"></i> Edit Invoice
    </a>
    @endif
    <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-outline-secondary" target="_blank">
        <i class="fas fa-file-pdf me-1"></i> Download PDF
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        {{-- Invoice Summary --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>{{ $invoice->invoice_number }}</h5>
                <x-status-badge :status="$invoice->status->value" />
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Patient</p>
                        <p class="fw-semibold mb-0">{{ $invoice->patient->full_name ?? '—' }}</p>
                        <p class="text-muted small">{{ $invoice->patient->mrn ?? '' }}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1 text-muted small">Doctor</p>
                        <p class="fw-semibold mb-0">Dr. {{ $invoice->doctor->full_name ?? '—' }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Created</p>
                        <p class="mb-0">{{ $invoice->created_at?->format('d M Y') }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted small">Due Date</p>
                        <p class="mb-0 {{ $invoice->due_date && $invoice->due_date->isPast() && $invoice->status->value !== 'paid' ? 'text-danger' : '' }}">
                            {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                        </p>
                    </div>
                    @if($invoice->notes)
                    <div class="col-12">
                        <p class="mb-1 text-muted small">Notes</p>
                        <p class="mb-0">{{ $invoice->notes }}</p>
                    </div>
                    @endif
                </div>

                {{-- Line Items --}}
                <h6 class="fw-semibold border-bottom pb-2 mb-3">Line Items</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                                @if($editable)<th></th>@endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->items as $item)
                            <tr>
                                <td>{{ $item->description }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total_price, 2) }}</td>
                                @if($editable)
                                <td class="text-end">
                                    <a href="{{ route('invoices.items.edit', [$invoice, $item]) }}"
                                       class="btn btn-xs btn-sm btn-outline-warning me-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('invoices.items.destroy', [$invoice, $item]) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-sm btn-outline-danger"
                                                onclick="return confirm('Remove this item?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end fw-semibold">Subtotal</td>
                                <td class="text-end">{{ number_format($invoice->subtotal, 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                            <tr>
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end">Tax ({{ $invoice->tax_rate }}%)</td>
                                <td class="text-end">{{ number_format($invoice->tax_amount, 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                            @if($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end">Discount</td>
                                <td class="text-end text-success">-{{ number_format($invoice->discount_amount, 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                            @endif
                            <tr class="fw-bold">
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end">Total</td>
                                <td class="text-end">{{ number_format($invoice->total_amount, 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                            <tr class="text-success">
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end">Amount Paid</td>
                                <td class="text-end">{{ number_format($invoice->amount_paid, 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                            <tr class="fw-bold text-danger">
                                <td colspan="{{ $editable ? 3 : 3 }}" class="text-end">Balance</td>
                                <td class="text-end">{{ number_format(max(0, $invoice->total_amount - $invoice->amount_paid), 2) }}</td>
                                @if($editable)<td></td>@endif
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($editable)
                <a href="{{ route('invoices.items.create', $invoice) }}" class="btn btn-sm btn-outline-primary mt-2">
                    <i class="fas fa-plus me-1"></i> Add Item
                </a>
                @endif
            </div>
        </div>

        {{-- Payments History --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Payment History</h6>
            </div>
            <div class="card-body p-0">
                @if($invoice->payments->isEmpty())
                    <p class="text-muted text-center py-3 mb-0">No payments recorded.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoice->payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date?->format('d M Y') }}</td>
                                <td>{{ number_format($payment->amount_paid, 2) }}</td>
                                <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method?->value ?? '')) }}</td>
                                <td>{{ $payment->reference_number ?? '—' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Record Payment --}}
    <div class="col-lg-4">
        @if($editable)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold">Record Payment</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('invoices.payments.store', $invoice) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" id="amount_paid" name="amount_paid"
                               class="form-control @error('amount_paid') is-invalid @enderror"
                               step="0.01" min="0.01"
                               value="{{ old('amount_paid', max(0, $invoice->total_amount - $invoice->amount_paid)) }}"
                               required>
                        @error('amount_paid')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                        <select id="payment_method" name="payment_method"
                                class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="">Select method</option>
                            @foreach(['cash' => 'Cash', 'card' => 'Card', 'mobile_money' => 'Mobile Money', 'bank_transfer' => 'Bank Transfer', 'insurance' => 'Insurance'] as $val => $label)
                            <option value="{{ $val }}" {{ old('payment_method') === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                        <input type="text" id="payment_date" name="payment_date"
                               class="form-control flatpickr-date @error('payment_date') is-invalid @enderror"
                               value="{{ old('payment_date', today()->format('Y-m-d')) }}" required>
                        @error('payment_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="reference_number" class="form-label">Reference #</label>
                        <input type="text" id="reference_number" name="reference_number"
                               class="form-control" value="{{ old('reference_number') }}">
                    </div>
                    <div class="mb-3">
                        <label for="pay_notes" class="form-label">Notes</label>
                        <textarea id="pay_notes" name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-money-bill-wave me-1"></i> Record Payment
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
</script>
@endpush

@extends('layouts.app')
@section('title', 'My Invoices')
@section('content')
<x-breadcrumb :items="[['label' => 'My Invoices']]" />
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>My Invoices</h4>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['unpaid','partial','paid','cancelled','refunded'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('patient.invoices.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Doctor</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="fw-semibold">{{ $inv->invoice_number }}</td>
                        <td>{{ $inv->created_at->format('d M Y') }}</td>
                        <td>{{ $inv->doctor ? 'Dr. ' . $inv->doctor->full_name : '—' }}</td>
                        <td>{{ number_format($inv->total_amount, 2) }}</td>
                        <td class="text-success">{{ number_format($inv->amount_paid, 2) }}</td>
                        <td class="{{ $inv->amount_paid < $inv->total_amount ? 'text-danger fw-semibold' : 'text-success' }}">
                            {{ number_format($inv->total_amount - $inv->amount_paid, 2) }}
                        </td>
                        <td>{{ $inv->due_date?->format('d M Y') ?? '—' }}</td>
                        <td><x-status-badge :status="$inv->status->value" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">No invoices found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($invoices->hasPages())
    <div class="card-footer bg-white">{!! $invoices->links() !!}</div>
    @endif
</div>
@endsection

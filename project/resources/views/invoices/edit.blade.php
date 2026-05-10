@extends('layouts.app')

@section('title', 'Edit Invoice')
@section('page-title', 'Edit Invoice')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Invoices', 'url' => route('invoices.index')],
    ['label' => $invoice->invoice_number, 'url' => route('invoices.show', $invoice)],
    ['label' => 'Edit'],
]" />

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0"><i class="fas fa-edit me-2 text-warning"></i>Edit Invoice — {{ $invoice->invoice_number }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('invoices.update', $invoice) }}">
            @csrf @method('PUT')

            <div class="row g-3">
                <div class="col-md-4">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="text" id="due_date" name="due_date"
                           class="form-control flatpickr-date @error('due_date') is-invalid @enderror"
                           value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}">
                    @error('due_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $invoice->notes) }}</textarea>
                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
</script>
@endpush

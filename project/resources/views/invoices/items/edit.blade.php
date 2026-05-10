@extends('layouts.app')

@section('title', 'Edit Invoice Item')
@section('page-title', 'Edit Invoice Item')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Invoices', 'url' => route('invoices.index')],
    ['label' => $invoice->invoice_number, 'url' => route('invoices.show', $invoice)],
    ['label' => 'Edit Item'],
]" />

<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Edit Line Item</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('invoices.items.update', [$invoice, $item]) }}">
            @csrf @method('PUT')
            <div class="mb-3">
                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                <input type="text" id="description" name="description"
                       class="form-control @error('description') is-invalid @enderror"
                       value="{{ old('description', $item->description) }}" required>
                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" id="quantity" name="quantity"
                           class="form-control @error('quantity') is-invalid @enderror"
                           value="{{ old('quantity', $item->quantity) }}" min="0.01" step="0.01" required>
                    @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="unit_price" class="form-label">Unit Price <span class="text-danger">*</span></label>
                    <input type="number" id="unit_price" name="unit_price"
                           class="form-control @error('unit_price') is-invalid @enderror"
                           value="{{ old('unit_price', $item->unit_price) }}" min="0" step="0.01" required>
                    @error('unit_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Patients Report')
@section('page-title', 'Patients Report')

@section('content')
<x-breadcrumb :items="[['label' => 'Patients Report']]" />

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.patients') }}" class="row g-2">
            <div class="col-md-3">
                <input type="number" name="year" class="form-control" placeholder="Year"
                       value="{{ $year }}" min="2020" max="{{ now()->year }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                <p class="text-muted small mb-1">Total Patients</p>
                <h3 class="fw-bold text-primary">{{ number_format($totalPatients) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-4">
                <p class="text-muted small mb-1">New This Month</p>
                <h3 class="fw-bold text-success">{{ number_format($newThisMonth) }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold">Registrations by Month ({{ $year }})</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Month</th><th class="text-end">Registrations</th></tr>
            </thead>
            <tbody>
                @forelse($registrations as $row)
                <tr>
                    <td>{{ date('F', mktime(0,0,0,$row->month,1)) }} {{ $row->year }}</td>
                    <td class="text-end">{{ $row->count }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center text-muted py-4">No data for {{ $year }}.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

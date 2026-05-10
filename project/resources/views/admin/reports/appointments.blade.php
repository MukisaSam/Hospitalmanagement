@extends('layouts.app')

@section('title', 'Appointments Report')
@section('page-title', 'Appointments Report')

@section('content')
<x-breadcrumb :items="[['label' => 'Appointments Report']]" />

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.appointments') }}" class="row g-2">
            <div class="col-md-2">
                <input type="text" name="from" class="form-control flatpickr-date" placeholder="From date"
                       value="{{ request('from') }}">
            </div>
            <div class="col-md-2">
                <input type="text" name="to" class="form-control flatpickr-date" placeholder="To date"
                       value="{{ request('to') }}">
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','confirmed','completed','cancelled','no_show'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['opd','ipd','emergency','follow_up'] as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ strtoupper($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.reports.appointments.export', request()->all()) }}"
                   class="btn btn-outline-success w-100">
                    <i class="fas fa-download me-1"></i> Export CSV
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->appointment_date?->format('d M Y') }}</td>
                        <td>{{ $appt->patient->full_name ?? '—' }}</td>
                        <td>{{ $appt->doctor->full_name ?? '—' }}</td>
                        <td>{{ strtoupper($appt->type?->value ?? '') }}</td>
                        <td><x-status-badge :status="$appt->status->value" /></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No appointments found for the selected filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $appointments->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
</script>
@endpush

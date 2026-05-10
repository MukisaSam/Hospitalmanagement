@extends('layouts.app')
@section('title', 'My Appointments')
@section('content')
<x-breadcrumb :items="[['label' => 'My Appointments']]" />
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i>My Appointments</h4>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-5">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','confirmed','checked_in','in_progress','completed','cancelled','no_show'] as $s)
                        <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Date</th><th>Time</th><th>Doctor</th><th>Department</th><th>Type</th><th>Status</th><th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->appointment_date->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('H:i') }}</td>
                        <td>Dr. {{ $appt->doctor->full_name }}<br>
                            <small class="text-muted">{{ $appt->doctor->specialization }}</small></td>
                        <td>{{ $appt->doctor->department?->name ?? '—' }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($appt->type->value) }}</span></td>
                        <td><x-status-badge :status="$appt->status->value" /></td>
                        <td class="text-muted small">{{ Str::limit($appt->notes, 40) ?: '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
    <div class="card-footer bg-white">{!! $appointments->links() !!}</div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Appointments')
@section('page-title', 'Appointments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <x-breadcrumb :items="[['label' => 'Appointments']]" />
    <a href="{{ route('appointments.create') }}" class="btn btn-primary">
        <i class="fas fa-calendar-plus me-1"></i> New Appointment
    </a>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('appointments.index') }}" class="row g-2">
            <div class="col-md-3">
                <input type="text" name="date" class="form-control flatpickr-date"
                       placeholder="Filter by date" value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <select name="doctor_id" class="form-select">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                        {{ $doctor->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','confirmed','checked_in','in_progress','completed','cancelled','no_show'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucwords(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->appointment_date?->format('d M Y') }}</td>
                        <td>{{ substr($appt->appointment_time, 0, 5) }}</td>
                        <td>
                            <a href="{{ route('patients.show', $appt->patient_id) }}">
                                {{ $appt->patient->full_name ?? '—' }}
                            </a>
                        </td>
                        <td>{{ $appt->doctor->full_name ?? '—' }}</td>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper($appt->type?->value ?? '') }}</span></td>
                        <td><x-status-badge :status="$appt->status->value" /></td>
                        <td>
                            <a href="{{ route('appointments.show', $appt) }}"
                               class="btn btn-sm btn-outline-primary" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $appointments->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d' });
</script>
@endpush

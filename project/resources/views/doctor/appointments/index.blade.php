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
            <div class="col-md-4">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-md-4">
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
                <a href="{{ route('doctor.appointments.index') }}" class="btn btn-outline-secondary">Reset</a>
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
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->appointment_date->format('d M Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($appt->appointment_time)->format('H:i') }}</td>
                        <td>{{ $appt->patient->full_name }}</td>
                        <td><span class="badge bg-secondary">{{ strtoupper($appt->type->value) }}</span></td>
                        <td><x-status-badge :status="$appt->status->value" /></td>
                        <td>
                            <div class="d-flex gap-1">
                                @if(in_array($appt->status->value, ['checked_in', 'in_progress']))
                                <form method="POST" action="{{ route('doctor.appointments.status', $appt) }}">
                                    @csrf @method('PATCH')
                                    @if($appt->status->value === 'checked_in')
                                        <input type="hidden" name="status" value="in_progress">
                                        <button class="btn btn-sm btn-warning">Start</button>
                                    @else
                                        <input type="hidden" name="status" value="completed">
                                        <button class="btn btn-sm btn-success">Complete</button>
                                    @endif
                                </form>
                                @endif
                                @if($appt->status->value === 'in_progress' || $appt->status->value === 'completed')
                                    @if($appt->medicalRecord)
                                        <a href="{{ route('doctor.medical-records.show', $appt->medicalRecord) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-medical"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('doctor.medical-records.create', ['appointment_id' => $appt->id]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-plus"></i> Record
                                        </a>
                                    @endif
                                @endif
                                @if(in_array($appt->status->value, ['confirmed', 'checked_in', 'in_progress']))
                                <form method="POST" action="{{ route('doctor.appointments.status', $appt) }}">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="status" value="no_show">
                                    <button class="btn btn-sm btn-outline-dark" onclick="return confirm('Mark as No-Show?')">No-Show</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No appointments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($appointments->hasPages())
    <div class="card-footer bg-white">
        {!! $appointments->links() !!}
    </div>
    @endif
</div>
@endsection

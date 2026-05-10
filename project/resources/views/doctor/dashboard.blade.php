@extends('layouts.app')
@section('title', 'Doctor Dashboard')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, Dr. {{ auth()->user()->name }}</h4>
        <p class="text-muted mb-0">{{ now()->format('l, d F Y') }}</p>
    </div>
</div>

<div class="row mb-4">
    <x-stat-card title="Today's Appointments" :value="$todayAppointments->count()" icon="calendar-check" color="primary" />
    <x-stat-card title="Pending / Upcoming" :value="$pendingAppointments" icon="clock" color="warning" />
    <x-stat-card title="Total Patients Treated" :value="$totalPatients" icon="users" color="success" />
    <x-stat-card title="Recent Records" :value="$recentRecords->count()" icon="file-medical" color="info" />
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold"><i class="fas fa-list me-1 text-primary"></i>Today's Appointments</span>
                <a href="{{ route('doctor.appointments.index', ['date' => today()->toDateString()]) }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                @if($todayAppointments->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-calendar-times fa-3x mb-3 opacity-50"></i>
                        <p>No appointments scheduled for today.</p>
                    </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayAppointments as $appt)
                            <tr>
                                <td class="fw-semibold">{{ \Carbon\Carbon::parse($appt->appointment_time)->format('H:i') }}</td>
                                <td>{{ $appt->patient->full_name }}</td>
                                <td><span class="badge bg-secondary">{{ strtoupper($appt->type->value) }}</span></td>
                                <td><x-status-badge :status="$appt->status->value" /></td>
                                <td>
                                    @if(in_array($appt->status->value, ['checked_in', 'in_progress']))
                                    <form method="POST" action="{{ route('doctor.appointments.status', $appt) }}" class="d-inline">
                                        @csrf @method('PATCH')
                                        @if($appt->status->value === 'checked_in')
                                            <input type="hidden" name="status" value="in_progress">
                                            <button class="btn btn-sm btn-warning">Start</button>
                                        @elseif($appt->status->value === 'in_progress')
                                            <input type="hidden" name="status" value="completed">
                                            <button class="btn btn-sm btn-success">Complete</button>
                                        @endif
                                    </form>
                                    @endif
                                    @if($appt->medicalRecord)
                                        <a href="{{ route('doctor.medical-records.show', $appt->medicalRecord) }}" class="btn btn-sm btn-outline-primary">Record</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">
                <i class="fas fa-file-medical me-1 text-success"></i>Recent Medical Records
            </div>
            <div class="card-body p-0">
                @if($recentRecords->isEmpty())
                    <div class="text-center text-muted py-4">
                        <p class="mb-0">No recent records.</p>
                    </div>
                @else
                <ul class="list-group list-group-flush">
                    @foreach($recentRecords as $record)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="fw-semibold small">{{ $record->patient->full_name }}</div>
                                <div class="text-muted small">{{ $record->visit_date->format('d M Y') }}</div>
                                <div class="small text-truncate" style="max-width:180px">{{ $record->diagnosis }}</div>
                            </div>
                            <a href="{{ route('doctor.medical-records.show', $record) }}" class="btn btn-sm btn-outline-secondary align-self-center">View</a>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

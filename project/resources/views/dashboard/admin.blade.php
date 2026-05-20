@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')

{{-- Stat cards --}}
<div class="row">
    <x-stat-card
        title="Total Patients"
        :value="number_format($stats['total_patients'])"
        icon="users"
        color="primary"
    />
    <x-stat-card
        title="Today's Appointments"
        :value="$stats['todays_appointments']"
        icon="calendar-check"
        color="success"
    />
    <x-stat-card
        title="Active Doctors"
        :value="$stats['active_doctors']"
        icon="user-md"
        color="info"
    />
    <x-stat-card
        title="Monthly Revenue"
        :value="'$' . number_format($stats['monthly_revenue'], 2)"
        icon="dollar-sign"
        color="warning"
    />
</div>

<div class="row mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card border-0 shadow-sm h-100 border-start border-danger border-4">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                    <i class="fas fa-file-invoice-dollar fa-lg text-danger"></i>
                </div>
                <div>
                    <div class="text-muted small">Pending Invoices</div>
                    <div class="fs-4 fw-bold">{{ $stats['pending_invoices'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts row --}}
<div class="row mb-4">
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>Appointments — Last 30 Days
                </h6>
            </div>
            <div class="card-body">
                <canvas id="appointmentsPerDayChart" height="120"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 pb-0">
                <h6 class="card-title mb-0 fw-semibold">
                    <i class="fas fa-chart-pie me-2 text-success"></i>By Type
                </h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="appointmentsByTypeChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Today's appointments table --}}
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0 fw-semibold">
            <i class="fas fa-calendar-day me-2 text-primary"></i>Today's Appointments
        </h6>
        <a href="{{ route('appointments.index') }}" class="btn btn-sm btn-outline-primary">
            View All
        </a>
    </div>
    <div class="card-body p-0">
        @if($todayAppointments->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                <p class="mb-0">No appointments scheduled for today.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todayAppointments as $appointment)
                            <tr>
                                <td class="fw-semibold">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('H:i') }}
                                </td>
                                <td>
                                    {{ $appointment->patient->first_name ?? '—' }}
                                    {{ $appointment->patient->last_name ?? '' }}
                                </td>
                                <td>
                                    Dr. {{ $appointment->doctor->last_name ?? '—' }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ ucwords(str_replace('_', ' ', $appointment->type?->value ?? 'N/A')) }}
                                    </span>
                                </td>
                                <td>
                                    <x-status-badge :status="$appointment->status->value" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
// Appointments per day — bar chart
fetch('/api/charts/appointments-per-day')
    .then(r => r.json())
    .then(({ labels, data }) => {
        new Chart(document.getElementById('appointmentsPerDayChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                    label: 'Appointments',
                    data,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: '#0d6efd',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    })
    .catch(() => {});

// Appointments by type — doughnut chart
fetch('/api/charts/appointments-by-type')
    .then(r => r.json())
    .then(({ labels, data }) => {
        new Chart(document.getElementById('appointmentsByTypeChart'), {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6c757d'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 10 } }
                }
            }
        });
    })
    .catch(() => {});
</script>
@endpush

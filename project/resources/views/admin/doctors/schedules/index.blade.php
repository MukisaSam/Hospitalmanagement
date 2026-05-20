@extends('layouts.app')

@section('title', 'Doctor Schedules')
@section('page-title', 'Doctor Schedules')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Doctors', 'url' => route('admin.doctors.index')],
    ['label' => 'Dr. ' . $doctor->full_name],
    ['label' => 'Schedules'],
]" />

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Dr. {{ $doctor->full_name }} — Weekly Schedule</h5>
    <a href="{{ route('admin.doctors.schedules.create', $doctor) }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i> Add Day
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Max Appointments</th>
                    <th>Available</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($doctor->schedules as $schedule)
                <tr>
                    <td>{{ ucfirst($schedule->day_of_week->value) }}</td>
                    <td>{{ substr($schedule->start_time, 0, 5) }}</td>
                    <td>{{ substr($schedule->end_time, 0, 5) }}</td>
                    <td>{{ $schedule->max_appointments }}</td>
                    <td>
                        @if($schedule->is_available)
                            <span class="badge bg-success">Yes</span>
                        @else
                            <span class="badge bg-secondary">No</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.schedules.edit', [$doctor, $schedule]) }}"
                           class="btn btn-sm btn-outline-warning me-1">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST"
                              action="{{ route('admin.schedules.destroy', [$doctor, $schedule]) }}"
                              class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                    onclick="return confirm('Remove this schedule?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No schedules set up yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

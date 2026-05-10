@extends('layouts.app')

@section('title', 'Add Schedule')
@section('page-title', 'Add Schedule')

@section('content')
<x-breadcrumb :items="[
    ['label' => 'Doctors', 'url' => route('admin.doctors.index')],
    ['label' => 'Dr. ' . $doctor->full_name, 'url' => route('admin.doctors.schedules.index', $doctor)],
    ['label' => 'Add Schedule'],
]" />

<div class="card border-0 shadow-sm" style="max-width:500px;">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Schedule for Dr. {{ $doctor->full_name }}</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.doctors.schedules.store', $doctor) }}">
            @csrf
            <div class="mb-3">
                <label for="day_of_week" class="form-label">Day <span class="text-danger">*</span></label>
                <select id="day_of_week" name="day_of_week"
                        class="form-select @error('day_of_week') is-invalid @enderror" required>
                    <option value="">Select day</option>
                    @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                    @if(!in_array($day, $existingDays))
                    <option value="{{ $day }}" {{ old('day_of_week') === $day ? 'selected' : '' }}>
                        {{ ucfirst($day) }}
                    </option>
                    @endif
                    @endforeach
                </select>
                @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                    <input type="text" id="start_time" name="start_time"
                           class="form-control flatpickr-time @error('start_time') is-invalid @enderror"
                           value="{{ old('start_time') }}" required>
                    @error('start_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                    <input type="text" id="end_time" name="end_time"
                           class="form-control flatpickr-time @error('end_time') is-invalid @enderror"
                           value="{{ old('end_time') }}" required>
                    @error('end_time')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label for="max_appointments" class="form-label">Max Appointments <span class="text-danger">*</span></label>
                <input type="number" id="max_appointments" name="max_appointments"
                       class="form-control @error('max_appointments') is-invalid @enderror"
                       value="{{ old('max_appointments', 10) }}" min="1" max="100" required>
                @error('max_appointments')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" id="is_available" name="is_available" value="1"
                       class="form-check-input" {{ old('is_available', true) ? 'checked' : '' }}>
                <label for="is_available" class="form-check-label">Available (accepting appointments)</label>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Save</button>
                <a href="{{ route('admin.doctors.schedules.index', $doctor) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    flatpickr('.flatpickr-time', { enableTime: true, noCalendar: true, dateFormat: 'H:i', time_24hr: true });
</script>
@endpush

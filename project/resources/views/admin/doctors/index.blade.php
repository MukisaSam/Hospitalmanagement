@extends('layouts.app')

@section('title', 'Doctors')
@section('page-title', 'Doctors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <x-breadcrumb :items="[['label' => 'Doctors']]" />
    <a href="{{ route('admin.doctors.create') }}" class="btn btn-primary">
        <i class="fas fa-user-md me-1"></i> Add Doctor
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.doctors.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or specialization..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="department_id" class="form-select">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="on_leave" {{ request('status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-1">
                <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
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
                        <th>Name</th>
                        <th>Specialization</th>
                        <th>Department</th>
                        <th>Fee</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                    <tr>
                        <td>Dr. {{ $doctor->full_name }}</td>
                        <td>{{ $doctor->specialization }}</td>
                        <td>{{ $doctor->department->name ?? '—' }}</td>
                        <td>{{ number_format($doctor->consultation_fee, 2) }}</td>
                        <td><x-status-badge :status="$doctor->status->value" /></td>
                        <td>
                            <a href="{{ route('admin.doctors.schedules.index', $doctor) }}"
                               class="btn btn-sm btn-outline-info me-1" title="Schedules">
                                <i class="fas fa-clock"></i>
                            </a>
                            <a href="{{ route('admin.doctors.edit', $doctor) }}"
                               class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="del-doc-{{ $doctor->id }}" method="POST"
                                  action="{{ route('admin.doctors.destroy', $doctor) }}" class="d-inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Archive"
                                    onclick="confirmArchive({{ $doctor->id }}, '{{ $doctor->full_name }}')">
                                <i class="fas fa-archive"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No doctors found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $doctors->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    function confirmArchive(id, name) {
        Swal.fire({
            title: 'Archive doctor?',
            text: 'Dr. ' + name + ' will be archived.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, archive'
        }).then(function (result) {
            if (result.isConfirmed) document.getElementById('del-doc-' + id).submit();
        });
    }
</script>
@endpush

@extends('layouts.app')

@section('title', 'Patients')
@section('page-title', 'Patients')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <x-breadcrumb :items="[['label' => 'Patients']]" />
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i> Register Patient
    </a>
</div>

{{-- Search --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('patients.index') }}" class="row g-2">
            <div class="col-md-8">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Search by name, MRN, phone or national ID..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Search
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="patients-table" class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>MRN</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                    <tr>
                        <td><code>{{ $patient->mrn }}</code></td>
                        <td>{{ $patient->full_name }}</td>
                        <td>{{ $patient->phone_number }}</td>
                        <td>{{ ucfirst($patient->gender?->value ?? '—') }}</td>
                        <td>{{ $patient->date_of_birth?->format('d M Y') ?? '—' }}</td>
                        <td>
                            <a href="{{ route('patients.show', $patient) }}"
                               class="btn btn-sm btn-outline-primary me-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('patients.edit', $patient) }}"
                               class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="delete-form-{{ $patient->id }}" method="POST"
                                  action="{{ route('patients.destroy', $patient) }}" class="d-inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Archive"
                                    onclick="confirmDelete({{ $patient->id }}, '{{ $patient->full_name }}')">
                                <i class="fas fa-archive"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No patients found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $patients->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#patients-table').DataTable({ pageLength: 25, order: [[0, 'asc']], searching: false, paging: false, info: false });

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Archive patient?',
            text: name + ' will be archived and removed from active lists.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, archive'
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endpush

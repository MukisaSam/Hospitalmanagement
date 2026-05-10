@extends('layouts.app')

@section('title', 'Departments')
@section('page-title', 'Departments')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <x-breadcrumb :items="[['label' => 'Departments']]" />
    <div class="d-flex gap-2">
        <a href="{{ route('admin.departments.archived') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-archive me-1"></i> Archived
        </a>
        <a href="{{ route('admin.departments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> New Department
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Head Doctor</th>
                        <th>Location</th>
                        <th>Phone Ext.</th>
                        <th>Doctors</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $dept)
                    <tr>
                        <td>{{ $dept->name }}</td>
                        <td>{{ $dept->headDoctor?->full_name ?? '—' }}</td>
                        <td>{{ $dept->location ?? '—' }}</td>
                        <td>{{ $dept->phone_extension ?? '—' }}</td>
                        <td>{{ $dept->doctors_count }}</td>
                        <td>
                            <a href="{{ route('admin.departments.edit', $dept) }}"
                               class="btn btn-sm btn-outline-warning me-1"><i class="fas fa-edit"></i></a>
                            <form id="del-dept-{{ $dept->id }}" method="POST"
                                  action="{{ route('admin.departments.destroy', $dept) }}" class="d-inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete('del-dept-{{ $dept->id }}', '{{ $dept->name }}')">
                                <i class="fas fa-archive"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No departments found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $departments->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(formId, name) {
        Swal.fire({
            title: 'Archive department?',
            text: name + ' will be archived.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, archive'
        }).then(function (result) {
            if (result.isConfirmed) document.getElementById(formId).submit();
        });
    }
</script>
@endpush

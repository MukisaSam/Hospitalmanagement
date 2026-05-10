@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <x-breadcrumb :items="[['label' => 'Users']]" />
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-user-plus me-1"></i> New User
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name or email..."
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    @foreach(['admin','doctor','receptionist','patient'] as $r)
                    <option value="{{ $r }}" {{ request('role') === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="users-table" class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td><span class="badge bg-info text-dark">{{ ucfirst($user->role->value) }}</span></td>
                        <td><x-status-badge :status="$user->status->value" /></td>
                        <td>{{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-secondary me-1" title="Toggle Status">
                                    <i class="fas fa-toggle-on"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.reset-password', $user) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-outline-info me-1"
                                        onclick="return confirm('Reset password for {{ $user->name }}?')" title="Reset Password">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                            @if($user->id !== auth()->id())
                            <form id="del-user-{{ $user->id }}" method="POST"
                                  action="{{ route('admin.users.destroy', $user) }}" class="d-inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-sm btn-outline-danger" title="Delete"
                                    onclick="confirmDeleteUser({{ $user->id }}, '{{ $user->name }}')">
                                <i class="fas fa-trash"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">{{ $users->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDeleteUser(id, name) {
        Swal.fire({
            title: 'Delete user?',
            text: 'This will permanently delete ' + name + '.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'Yes, delete'
        }).then(function (result) {
            if (result.isConfirmed) {
                document.getElementById('del-user-' + id).submit();
            }
        });
    }
</script>
@endpush

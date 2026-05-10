<nav class="sidebar d-flex flex-column p-3" id="sidebar">
    <a href="{{ route('dashboard') }}" class="text-white text-decoration-none mb-3 d-flex align-items-center gap-2">
        <i class="fas fa-hospital fa-lg"></i>
        <span class="fw-bold fs-5">HMS</span>
    </a>
    <hr class="text-secondary">

    <ul class="nav flex-column gap-1">
        {{-- Dashboard --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
               href="{{ route('dashboard') }}">
                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
            </a>
        </li>

        {{-- Admin + Receptionist --}}
        @if(in_array(auth()->user()->role->value, ['admin', 'receptionist']))
        <li><span class="nav-section">Patient Management</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}"
               href="{{ route('patients.index') }}">
                <i class="fas fa-users me-2"></i> Patients
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}"
               href="{{ route('appointments.index') }}">
                <i class="fas fa-calendar-check me-2"></i> Appointments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}"
               href="{{ route('invoices.index') }}">
                <i class="fas fa-file-invoice-dollar me-2"></i> Invoices
            </a>
        </li>
        @endif

        {{-- Admin only --}}
        @if(auth()->user()->role->value === 'admin')
        <li><span class="nav-section">Administration</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
               href="{{ route('admin.users.index') }}">
                <i class="fas fa-user-cog me-2"></i> Users
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}"
               href="{{ route('admin.departments.index') }}">
                <i class="fas fa-building me-2"></i> Departments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}"
               href="{{ route('admin.doctors.index') }}">
                <i class="fas fa-user-md me-2"></i> Doctors
            </a>
        </li>
        <li><span class="nav-section">Reports</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.appointments') ? 'active' : '' }}"
               href="{{ route('admin.reports.appointments') }}">
                <i class="fas fa-chart-bar me-2"></i> Appointments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}"
               href="{{ route('admin.reports.revenue') }}">
                <i class="fas fa-chart-line me-2"></i> Revenue
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.reports.patients') ? 'active' : '' }}"
               href="{{ route('admin.reports.patients') }}">
                <i class="fas fa-chart-pie me-2"></i> Patients
            </a>
        </li>
        <li><span class="nav-section">System</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}"
               href="{{ route('admin.audit-logs.index') }}">
                <i class="fas fa-history me-2"></i> Audit Logs
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}"
               href="{{ route('admin.settings.edit') }}">
                <i class="fas fa-cog me-2"></i> Settings
            </a>
        </li>
        @endif

        {{-- Doctor --}}
        @if(auth()->user()->role->value === 'doctor')
        <li><span class="nav-section">My Work</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}"
               href="{{ route('doctor.dashboard') }}">
                <i class="fas fa-home me-2"></i> My Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}"
               href="{{ route('doctor.appointments.index') }}">
                <i class="fas fa-calendar-check me-2"></i> My Appointments
            </a>
        </li>
        @endif

        {{-- Patient --}}
        @if(auth()->user()->role->value === 'patient')
        <li><span class="nav-section">My Account</span></li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}"
               href="{{ route('patient.dashboard') }}">
                <i class="fas fa-home me-2"></i> My Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}"
               href="{{ route('patient.appointments.index') }}">
                <i class="fas fa-calendar-check me-2"></i> My Appointments
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patient.medical-records.*') ? 'active' : '' }}"
               href="{{ route('patient.medical-records.index') }}">
                <i class="fas fa-file-medical me-2"></i> Medical Records
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('patient.invoices.*') ? 'active' : '' }}"
               href="{{ route('patient.invoices.index') }}">
                <i class="fas fa-file-invoice me-2"></i> My Invoices
            </a>
        </li>
        @endif
    </ul>

    <div class="mt-auto pt-3 border-top border-secondary">
        <a class="nav-link text-muted" href="{{ route('profile') }}">
            <i class="fas fa-user-circle me-2"></i> {{ auth()->user()->name }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link nav-link text-danger p-0">
                <i class="fas fa-sign-out-alt me-2"></i> Logout
            </button>
        </form>
    </div>
</nav>

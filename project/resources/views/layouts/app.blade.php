<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HMS</title>

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.4 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --sidebar-width: 260px;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: #1a2236;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            text-decoration: none;
        }

        .sidebar-brand:hover {
            text-decoration: none;
        }

        .sidebar-nav {
            flex: 1;
            padding: 1rem 0;
            overflow-y: auto;
        }

        .sidebar-nav .nav-section {
            padding: 0.5rem 1rem 0.25rem;
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: rgba(255, 255, 255, 0.35);
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.875rem;
            border-radius: 0;
            transition: background 0.15s, color 0.15s;
            text-decoration: none;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.07);
            color: #fff;
        }

        .sidebar-nav .nav-link.active {
            background: #0d6efd;
            color: #fff;
        }

        .sidebar-nav .nav-link .nav-icon {
            width: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        /* Main content wrapper */
        #main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        #topbar {
            background: #fff;
            border-bottom: 1px solid #e5e9f0;
            padding: 0 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        /* Page content */
        #page-content {
            flex: 1;
            padding: 1.5rem;
        }

        /* Responsive sidebar collapse */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }

            #sidebar.show {
                transform: translateX(0);
            }

            #main-wrapper {
                margin-left: 0;
            }

            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1039;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Role badge in sidebar footer */
        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- Sidebar -->
<nav id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand d-flex align-items-center gap-2">
        <i class="fas fa-hospital text-primary fs-5"></i>
        <span class="fw-bold text-white fs-6">HMS</span>
    </a>

    <div class="sidebar-nav">
        <!-- Dashboard — all roles -->
        <span class="nav-section">Main</span>
        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
            Dashboard
        </a>

        @php $role = auth()->user()->role->value; @endphp

        {{-- Admin + Receptionist sections --}}
        @if(in_array($role, ['admin', 'receptionist']))
            <span class="nav-section mt-2">Patient Care</span>

            <a href="{{ route('patients.index') }}"
               class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                Patients
            </a>

            <a href="{{ route('appointments.index') }}"
               class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-calendar-check"></i></span>
                Appointments
            </a>

            <a href="{{ route('invoices.index') }}"
               class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                Invoices
            </a>
        @endif

        {{-- Admin-only sections --}}
        @if($role === 'admin')
            <span class="nav-section mt-2">Administration</span>

            <a href="{{ route('admin.users.index') }}"
               class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-cog"></i></span>
                Users
            </a>

            <a href="{{ route('admin.departments.index') }}"
               class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-sitemap"></i></span>
                Departments
            </a>

            <a href="{{ route('admin.doctors.index') }}"
               class="nav-link {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-user-md"></i></span>
                Doctors
            </a>

            <span class="nav-section mt-2">Reports &amp; Config</span>

            <a href="{{ route('admin.reports.appointments') }}"
               class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                Reports
            </a>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="nav-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span>
                Audit Logs
            </a>

            <a href="{{ route('admin.settings.edit') }}"
               class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                Settings
            </a>
        @endif

        {{-- Doctor sections --}}
        @if($role === 'doctor')
            <span class="nav-section mt-2">My Work</span>

            <a href="{{ route('doctor.appointments.index') }}"
               class="nav-link {{ request()->routeIs('doctor.appointments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                My Appointments
            </a>

            <a href="{{ route('doctor.medical-records.index') }}"
               class="nav-link {{ request()->routeIs('doctor.medical-records.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-notes-medical"></i></span>
                Medical Records
            </a>
        @endif

        {{-- Patient sections --}}
        @if($role === 'patient')
            <span class="nav-section mt-2">My Health</span>

            <a href="{{ route('patient.appointments.index') }}"
               class="nav-link {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-calendar-alt"></i></span>
                My Appointments
            </a>

            <a href="{{ route('patient.medical-records.index') }}"
               class="nav-link {{ request()->routeIs('patient.medical-records.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-file-medical"></i></span>
                My Records
            </a>

            <a href="{{ route('patient.invoices.index') }}"
               class="nav-link {{ request()->routeIs('patient.invoices.*') ? 'active' : '' }}">
                <span class="nav-icon"><i class="fas fa-receipt"></i></span>
                My Invoices
            </a>
        @endif

        {{-- Profile — all roles --}}
        <span class="nav-section mt-2">Account</span>
        <a href="{{ route('profile') }}"
           class="nav-link {{ request()->routeIs('profile*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
            Profile
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary bg-opacity-20 d-flex align-items-center justify-content-center"
                 style="width:34px;height:34px;flex-shrink:0;">
                <i class="fas fa-user text-primary small"></i>
            </div>
            <div class="overflow-hidden">
                <div class="text-white small fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                <div class="text-white-50" style="font-size:0.7rem;">
                    {{ ucfirst(auth()->user()->role->value) }}
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Main wrapper -->
<div id="main-wrapper">

    <!-- Topbar -->
    <header id="topbar">
        <div class="d-flex align-items-center gap-3">
            <!-- Mobile sidebar toggle -->
            <button class="btn btn-sm btn-outline-secondary d-lg-none" onclick="openSidebar()" aria-label="Open menu">
                <i class="fas fa-bars"></i>
            </button>
            <h6 class="mb-0 fw-semibold text-dark">@yield('page-title', 'Dashboard')</h6>
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-muted small d-none d-md-inline">
                <i class="fas fa-calendar-day me-1"></i>{{ now()->format('D, d M Y') }}
            </span>

            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fas fa-user-circle"></i>
                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <span class="dropdown-item-text text-muted small">
                            {{ auth()->user()->email }}
                        </span>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="{{ route('profile') }}">
                            <i class="fas fa-user-circle me-2 text-muted"></i>Profile
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Page content -->
    <main id="page-content">
        @include('components.alert')

        @yield('content')
    </main>

</div>

<!-- Bootstrap 5.3 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery (required for DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Chart.js 4.x -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.add('show');
        document.getElementById('sidebarOverlay').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('show');
        document.getElementById('sidebarOverlay').classList.remove('show');
        document.body.style.overflow = '';
    }
</script>

@stack('scripts')

</body>
</html>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('app.name', 'Hospital Management') }} — @yield('title', 'Dashboard')</title>

{{-- Bootstrap 5.3 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
{{-- Font Awesome 6 --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
{{-- DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
{{-- Flatpickr --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>
    body { background-color: #f8f9fa; }
    .sidebar {
        min-height: 100vh;
        width: 250px;
        background-color: #212529;
        position: fixed;
        top: 0; left: 0;
        overflow-y: auto;
        z-index: 1000;
    }
    .main-content { margin-left: 250px; }
    .sidebar .nav-link { color: #adb5bd; padding: .5rem 1rem; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background-color: #343a40; border-radius: 4px; }
    .sidebar .nav-section { color: #6c757d; font-size: 0.75rem; text-transform: uppercase; padding: .5rem 1rem; margin-top: .5rem; }
    @media (max-width: 991.98px) {
        .sidebar { transform: translateX(-100%); transition: transform 0.3s; }
        .sidebar.show { transform: translateX(0); }
        .main-content { margin-left: 0; }
    }
</style>
@stack('styles')

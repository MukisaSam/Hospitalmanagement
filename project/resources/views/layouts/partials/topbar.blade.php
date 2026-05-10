<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-2">
    <button class="btn btn-outline-secondary d-lg-none me-2" type="button" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <span class="navbar-brand mb-0 h6 text-muted">@yield('title', 'Dashboard')</span>
    <div class="ms-auto d-flex align-items-center gap-3">
        <span class="badge bg-secondary text-uppercase">{{ auth()->user()->role->value }}</span>
        <span class="text-muted small">{{ now()->format('D, d M Y') }}</span>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('sidebarToggle');
        var sidebar = document.getElementById('sidebar');
        if (toggle && sidebar) {
            toggle.addEventListener('click', function () {
                sidebar.classList.toggle('show');
            });
        }
    });
</script>

# Frontend Rules — Hospital Management System

## Stack

- **Templates:** Laravel Blade (server-rendered, no SPA)
- **CSS:** Bootstrap 5.3 (via CDN or compiled with Vite)
- **Icons:** Font Awesome 6.x
- **Charts:** Chart.js 4.x (dashboard only, fed by internal JSON endpoints)
- **Date/time picker:** Flatpickr 4.x (appointment booking forms)
- **Tables:** DataTables.js 1.13 (patient list, appointment list, invoice list)
- **Confirmation dialogs:** SweetAlert2 11.x (destructive actions)
- **No Vue, React, or Alpine.js.** Plain JavaScript only where interaction is needed.

---

## Layout Structure

### Files

```
resources/views/
  layouts/
    app.blade.php      ← Authenticated layout (sidebar + topbar + content slot)
    guest.blade.php    ← Login/forgot-password layout (centred card)
  components/
    alert.blade.php        ← Flash message banner (success/error/warning)
    breadcrumb.blade.php   ← Breadcrumb trail
    stat-card.blade.php    ← Dashboard metric card
    status-badge.blade.php ← Coloured + labelled status pill
```

### `app.blade.php` Structure

```html
<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.partials.head')   {{-- meta, CSS links --}}
</head>
<body>
  <div class="d-flex">
    @include('layouts.partials.sidebar')   {{-- role-aware nav --}}
    <div class="flex-grow-1">
      @include('layouts.partials.topbar')
      <main class="p-4">
        @include('components.alert')
        @yield('content')
      </main>
    </div>
  </div>
  @include('layouts.partials.scripts')   {{-- JS at bottom --}}
  @stack('scripts')                      {{-- page-level JS --}}
</body>
</html>
```

---

## Blade Conventions

- Use `@extends('layouts.app')` and `@section('content')` on every authenticated view.
- Use `@extends('layouts.guest')` on login and password reset views.
- Push page-specific scripts with `@push('scripts')` — never inline `<script>` inside `@section('content')`.
- All form actions must use named route helpers: `route('patients.store')`, never hard-coded paths.
- Method spoofing for PUT/PATCH/DELETE: always include `@method('PUT')` and `@csrf` inside the form.
- Never echo raw user input — always use `{{ }}` (escaped), never `{!! !!}` unless the source is trusted HTML (e.g., a system-generated value).

---

## Forms

### Validation Error Display

Every input must show its error adjacent to the field:

```html
<div class="mb-3">
  <label for="first_name" class="form-label">First Name</label>
  <input type="text"
         id="first_name"
         name="first_name"
         class="form-control @error('first_name') is-invalid @enderror"
         value="{{ old('first_name', $patient->first_name ?? '') }}">
  @error('first_name')
    <div class="invalid-feedback">{{ $message }}</div>
  @enderror
</div>
```

- Use `old()` to repopulate fields after a failed submission. Always provide a fallback: `old('field', $model->field ?? '')`.
- Required fields must have the HTML `required` attribute AND server-side validation.

### Date and Time Fields

Use Flatpickr for all date and time inputs on appointment forms:

```html
<input type="text" id="appointment_date" name="appointment_date"
       class="form-control flatpickr-date"
       value="{{ old('appointment_date') }}"
       placeholder="YYYY-MM-DD">

@push('scripts')
<script>
  flatpickr('.flatpickr-date', { dateFormat: 'Y-m-d', minDate: 'today' });
</script>
@endpush
```

---

## Flash Messages

Flash a success message on every successful create/update/delete/payment. Set in the controller:

```php
return redirect()->route('patients.index')->with('success', 'Patient registered successfully.');
```

The `components/alert.blade.php` component reads `session('success')`, `session('error')`, and `session('warning')` and renders Bootstrap alerts.

---

## Status Badges

Always use the `<x-status-badge :status="$appointment->status" />` component. Never render raw status strings directly in tables.

The component maps statuses to Bootstrap badge colours:

| Status | Badge colour |
|--------|-------------|
| pending | `bg-secondary` |
| confirmed | `bg-primary` |
| checked_in | `bg-info text-dark` |
| in_progress | `bg-warning text-dark` |
| completed | `bg-success` |
| cancelled | `bg-danger` |
| no_show | `bg-dark` |
| unpaid | `bg-danger` |
| partial | `bg-warning text-dark` |
| paid | `bg-success` |

---

## Tables (DataTables)

Apply DataTables to any listing table with more than one page of data:

```html
<table id="patients-table" class="table table-hover table-bordered">
  <thead>...</thead>
  <tbody>...</tbody>
</table>

@push('scripts')
<script>
  $('#patients-table').DataTable({ pageLength: 25, order: [[0, 'asc']] });
</script>
@endpush
```

- Disable DataTables on the appointment queue (FR-APPT-06) — that table uses server-side pagination.
- Keep server-side `->paginate(25)` on all controller queries. DataTables is for client-side sort/search on already-paginated results.

---

## Destructive Action Confirmations

Use SweetAlert2 for any delete, cancel, or deactivate action:

```html
<form id="delete-form-{{ $patient->id }}" method="POST"
      action="{{ route('patients.destroy', $patient) }}">
  @csrf @method('DELETE')
</form>
<button type="button" class="btn btn-danger btn-sm"
        onclick="confirmDelete({{ $patient->id }}, 'patient')">
  Delete
</button>

@push('scripts')
<script>
function confirmDelete(id, type) {
  Swal.fire({
    title: 'Are you sure?',
    text: 'This ' + type + ' will be archived and cannot be recovered easily.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Yes, delete it'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('delete-form-' + id).submit();
    }
  });
}
</script>
@endpush
```

---

## Dashboard Charts

Charts use Chart.js loaded from CDN. Data is fetched from internal `/api/charts/*` endpoints via `fetch()` on page load.

```javascript
fetch('/api/charts/appointments-per-day')
  .then(r => r.json())
  .then(({ labels, data }) => {
    new Chart(document.getElementById('apptChart'), {
      type: 'bar',
      data: { labels, datasets: [{ label: 'Appointments', data, backgroundColor: '#0d6efd' }] },
      options: { responsive: true, plugins: { legend: { display: false } } }
    });
  });
```

- Each chart canvas must have a unique `id`.
- Charts are only rendered on dashboard views — do not load Chart.js globally.
- Use `@push('scripts')` to scope chart JS to the dashboard view.

---

## Sidebar Navigation

The sidebar must render only the menu items the authenticated user's role is permitted to access:

```blade
{{-- Always visible --}}
<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
     href="{{ route('dashboard') }}">Dashboard</a>
</li>

{{-- Admin + Receptionist --}}
@if(in_array(auth()->user()->role, ['admin', 'receptionist']))
<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('patients.*') ? 'active' : '' }}"
     href="{{ route('patients.index') }}">Patients</a>
</li>
@endif

{{-- Admin only --}}
@if(auth()->user()->role === 'admin')
<li class="nav-item">
  <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
     href="{{ route('admin.users.index') }}">Users</a>
</li>
@endif
```

Use `request()->routeIs('patients.*')` to highlight the active section, not URL matching.

---

## Breadcrumbs

Every detail/edit/create view must include a breadcrumb using the `<x-breadcrumb>` component:

```blade
<x-breadcrumb :items="[
  ['label' => 'Patients', 'url' => route('patients.index')],
  ['label' => $patient->full_name, 'url' => route('patients.show', $patient)],
  ['label' => 'Edit'],
]" />
```

The last item (current page) has no `url` and is rendered as plain text.

---

## Accessibility

- Every `<input>`, `<select>`, `<textarea>` must have a matching `<label for="...">`.
- Every `<img>` must have an `alt` attribute. Profile photo placeholders use `alt="Profile photo of {name}"`.
- Do not convey state through colour alone — status badges always include the text label.
- Form error messages use `role="alert"` to surface to screen readers (Bootstrap's `.invalid-feedback` does this automatically).

---

## Responsiveness

- All layouts use Bootstrap 5 grid (`col-md-*`, `col-lg-*`).
- Minimum supported viewport: 768px (tablet landscape). Do not test below this.
- The sidebar collapses to an off-canvas drawer on screens below `lg` breakpoint.
- Tables are wrapped in `<div class="table-responsive">` to allow horizontal scroll on smaller screens.

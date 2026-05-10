# API Rules — Hospital Management System

## Overview

The HMS exposes no public REST API in v1. All data is served server-side via Blade views. The only JSON endpoints are internal dashboard chart feeds consumed by Chart.js on the same authenticated session.

---

## Internal JSON Endpoints

### Conventions

- Route prefix: `/api/` is reserved for internal chart/data endpoints only.
- All `/api/*` routes must be inside the `auth` middleware group and additionally protected by the `CheckRole` middleware.
- Return `application/json` responses using Laravel's `response()->json()`.
- Never return raw Eloquent collections — always transform with `->map()` or an API Resource if the shape is reused.

### Dashboard Data Endpoints

| Endpoint | Method | Role Access | Description |
|----------|--------|-------------|-------------|
| `/api/charts/appointments-per-day` | GET | admin, receptionist | Last 30 days appointment count grouped by date |
| `/api/charts/appointments-by-type` | GET | admin, receptionist | Count per appointment type (opd/ipd/emergency/follow_up) |
| `/api/charts/revenue-per-month` | GET | admin | Monthly revenue sum for the last 12 months |
| `/api/charts/patient-registrations` | GET | admin | New patient registrations per month, last 12 months |

### Response Shape

All chart endpoints return a consistent envelope:

```json
{
  "labels": ["2026-04-10", "2026-04-11"],
  "data": [12, 7]
}
```

### Error Responses

- `401` — unauthenticated (session expired)
- `403` — authenticated but wrong role
- `422` — invalid query parameters
- `500` — server error (log to `storage/logs/laravel.log`, return generic message)

Never expose stack traces or model details in JSON error responses.

---

## Route Organisation (`routes/web.php`)

Group routes by role. Every authenticated group must carry both `auth` and `check.role` middleware:

```php
// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password', ...)->name('password.request');
    Route::post('/forgot-password', ...)->name('password.email');
    Route::get('/reset-password/{token}', ...)->name('password.reset');
    Route::post('/reset-password', ...)->name('password.update');
});

// Shared authenticated routes (all roles)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update']);
});

// Admin-only routes
Route::middleware(['auth', 'check.role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', Admin\UserController::class);
    Route::resource('departments', Admin\DepartmentController::class);
    Route::resource('doctors', Admin\DoctorController::class);
    Route::resource('doctors.schedules', Admin\DoctorScheduleController::class);
    Route::get('reports/appointments', [Admin\ReportController::class, 'appointments'])->name('reports.appointments');
    Route::get('reports/revenue', [Admin\ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/patients', [Admin\ReportController::class, 'patients'])->name('reports.patients');
    Route::get('audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [Admin\SettingController::class, 'update'])->name('settings.update');
});

// Admin + Receptionist routes
Route::middleware(['auth', 'check.role:admin,receptionist'])->group(function () {
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('invoices.items', InvoiceItemController::class)->except(['show']);
    Route::resource('invoices.payments', PaymentController::class)->only(['store']);
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
});

// Doctor routes
Route::middleware(['auth', 'check.role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('dashboard', [Doctor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('appointments', [Doctor\AppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('appointments/{appointment}/status', [Doctor\AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::resource('medical-records', Doctor\MedicalRecordController::class)->except(['index', 'destroy']);
    Route::resource('medical-records.prescriptions', Doctor\PrescriptionController::class)->except(['index', 'show']);
    Route::resource('medical-records.vitals', Doctor\VitalController::class)->only(['store', 'update']);
});

// Patient routes
Route::middleware(['auth', 'check.role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('dashboard', [Patient\DashboardController::class, 'index'])->name('dashboard');
    Route::get('appointments', [Patient\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('medical-records', [Patient\MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('invoices', [Patient\InvoiceController::class, 'index'])->name('invoices.index');
});

// Internal chart API
Route::middleware(['auth'])->prefix('api/charts')->name('api.charts.')->group(function () {
    Route::get('appointments-per-day', [ChartController::class, 'appointmentsPerDay']);
    Route::get('appointments-by-type', [ChartController::class, 'appointmentsByType']);
    Route::get('revenue-per-month', [ChartController::class, 'revenuePerMonth']);
    Route::get('patient-registrations', [ChartController::class, 'patientRegistrations']);
});
```

---

## CSV Export Endpoints

- Export routes live alongside their report routes and return `response()->streamDownload()`.
- Filename format: `{report-type}-{Y-m-d}.csv` (e.g., `appointments-2026-05-10.csv`).
- Headers must be the first row of the CSV.
- Use chunked queries (`->chunk(500, ...)`) for large exports to avoid memory exhaustion.

---

## Controller Conventions

- One controller per resource. Do not add extra action methods beyond the 7 standard resourceful ones plus explicit named extras (e.g., `updateStatus`, `pdf`).
- Controllers must contain no business logic — delegate to Service classes.
- Always use Form Request classes for validation; never call `$request->validate()` directly in the controller.
- Redirect after successful POST/PUT/DELETE (PRG pattern). Never return a view from a POST route.
- Use named routes everywhere. Never hard-code URLs in controllers or views.

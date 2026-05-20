<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\DoctorScheduleController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\MedicalRecordController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Doctor\VitalController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\InvoiceController as PatientInvoiceController;
use App\Http\Controllers\Patient\MedicalRecordController as PatientMedicalRecordController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Root redirect — authenticated users go to their dashboard, guests to login
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// -------------------------------------------------------------------------
// Guest routes
// -------------------------------------------------------------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/forgot-password', [PasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordController::class, 'resetPassword'])->name('password.update');
});

// -------------------------------------------------------------------------
// Shared authenticated routes (all roles)
// -------------------------------------------------------------------------
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// -------------------------------------------------------------------------
// Admin-only routes
// -------------------------------------------------------------------------
Route::middleware(['auth', 'check.role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User management
        Route::resource('users', UserController::class);
        Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::post('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

        // Department management (archive/restore must be defined before the resource
        // so that 'archived' is not treated as a wildcard {department} segment)
        Route::get('departments/archived', [DepartmentController::class, 'archived'])->name('departments.archived');
        Route::post('departments/{id}/restore', [DepartmentController::class, 'restore'])->name('departments.restore');
        Route::resource('departments', DepartmentController::class);

        // Doctor management
        Route::resource('doctors', DoctorController::class);
        Route::resource('doctors.schedules', DoctorScheduleController::class)->shallow();

        // Reports
        Route::get('reports/appointments', [ReportController::class, 'appointments'])->name('reports.appointments');
        Route::get('reports/appointments/export', [ReportController::class, 'exportAppointments'])->name('reports.appointments.export');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/revenue/export', [ReportController::class, 'exportRevenue'])->name('reports.revenue.export');
        Route::get('reports/patients', [ReportController::class, 'patients'])->name('reports.patients');

        // Audit logs (read-only)
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

        // Settings
        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');

        // Soft-deleted patient management (admin only)
        Route::get('patients/archived', [PatientController::class, 'archived'])->name('patients.archived');
        Route::post('patients/{id}/restore', [PatientController::class, 'restore'])->name('patients.restore');
    });

// -------------------------------------------------------------------------
// Admin + Receptionist routes
// -------------------------------------------------------------------------
Route::middleware(['auth', 'check.role:admin,receptionist'])->group(function () {
    Route::resource('patients', PatientController::class);

    Route::resource('appointments', AppointmentController::class);
    Route::patch('appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');

    Route::resource('invoices', InvoiceController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('invoices.items', InvoiceItemController::class)->except(['show']);
    Route::resource('invoices.payments', PaymentController::class)->only(['store']);
    Route::patch('invoices/{invoice}/payments/{payment}/confirm', [PaymentController::class, 'confirm'])
        ->name('invoices.payments.confirm');
    Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
});

// -------------------------------------------------------------------------
// Doctor routes
// -------------------------------------------------------------------------
Route::middleware(['auth', 'check.role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

        Route::get('appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.status');

        Route::resource('medical-records', MedicalRecordController::class)->except(['destroy']);
        Route::resource('medical-records.prescriptions', PrescriptionController::class)->except(['index', 'show']);
        Route::resource('medical-records.vitals', VitalController::class)->only(['store', 'update']);
    });

// -------------------------------------------------------------------------
// Patient routes
// -------------------------------------------------------------------------
Route::middleware(['auth', 'check.role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->group(function () {
        Route::get('dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        Route::get('appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('medical-records', [PatientMedicalRecordController::class, 'index'])->name('medical-records.index');
        Route::get('invoices', [PatientInvoiceController::class, 'index'])->name('invoices.index');
    });

// -------------------------------------------------------------------------
// Internal chart API (authenticated, no role restriction — controllers
// enforce per-endpoint role checks via CheckRole middleware if needed)
// -------------------------------------------------------------------------
Route::middleware(['auth'])
    ->prefix('api/charts')
    ->name('api.charts.')
    ->group(function () {
        Route::get('appointments-per-day', [ChartController::class, 'appointmentsPerDay'])->name('appointments-per-day');
        Route::get('appointments-by-type', [ChartController::class, 'appointmentsByType'])->name('appointments-by-type');
        Route::get('revenue-per-month', [ChartController::class, 'revenuePerMonth'])->name('revenue-per-month');
        Route::get('patient-registrations', [ChartController::class, 'patientRegistrations'])->name('patient-registrations');
    });

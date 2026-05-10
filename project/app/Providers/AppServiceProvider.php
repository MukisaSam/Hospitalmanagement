<?php

namespace App\Providers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use App\Observers\AppointmentObserver;
use App\Observers\DoctorObserver;
use App\Observers\InvoiceObserver;
use App\Observers\MedicalRecordObserver;
use App\Observers\PatientObserver;
use App\Observers\PaymentObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Patient::observe(PatientObserver::class);
        User::observe(UserObserver::class);
        Doctor::observe(DoctorObserver::class);
        Appointment::observe(AppointmentObserver::class);
        MedicalRecord::observe(MedicalRecordObserver::class);
        Invoice::observe(InvoiceObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}

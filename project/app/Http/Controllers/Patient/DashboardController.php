<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $patient = auth()->user()->patient;
        abort_unless($patient, 403);

        $upcomingAppointments = $patient->appointments()
            ->with('doctor', 'doctor.department')
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->whereDate('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->limit(5)
            ->get();

        $recentRecords = $patient->medicalRecords()
            ->with('doctor')
            ->orderBy('visit_date', 'desc')
            ->limit(3)
            ->get();

        $outstandingInvoices = $patient->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->get();

        return view('patient.dashboard', compact(
            'patient',
            'upcomingAppointments',
            'recentRecords',
            'outstandingInvoices'
        ));
    }
}

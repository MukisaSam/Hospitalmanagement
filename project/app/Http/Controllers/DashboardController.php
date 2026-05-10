<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role->value;

        if ($role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        }

        if ($role === 'patient') {
            return redirect()->route('patient.dashboard');
        }

        // admin or receptionist
        $stats = [
            'total_patients'      => Patient::count(),
            'todays_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'active_doctors'      => Doctor::where('status', 'active')->count(),
            'monthly_revenue'     => Invoice::whereMonth('created_at', now()->month)->sum('amount_paid'),
            'pending_invoices'    => Invoice::whereIn('status', ['unpaid', 'partial'])->count(),
        ];

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        return view('dashboard.admin', compact('stats', 'todayAppointments'));
    }
}

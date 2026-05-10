<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $doctor = auth()->user()->doctor;

        abort_unless($doctor, 403);

        $todayAppointments = $doctor->appointments()
            ->with('patient')
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        $totalPatients = $doctor->appointments()
            ->distinct('patient_id')
            ->count('patient_id');

        $recentRecords = \App\Models\MedicalRecord::where('doctor_id', $doctor->id)
            ->with('patient')
            ->orderBy('visit_date', 'desc')
            ->limit(5)
            ->get();

        $pendingAppointments = $doctor->appointments()
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->whereDate('appointment_date', '>=', today())
            ->count();

        return view('doctor.dashboard', compact(
            'doctor',
            'todayAppointments',
            'totalPatients',
            'recentRecords',
            'pendingAppointments'
        ));
    }
}

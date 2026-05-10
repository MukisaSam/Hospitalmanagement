<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $patient = auth()->user()->patient;
        abort_unless($patient, 403);

        $query = $patient->appointments()->with(['doctor', 'doctor.department']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')->paginate(25)->withQueryString();

        return view('patient.appointments.index', compact('appointments'));
    }
}

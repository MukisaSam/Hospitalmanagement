<?php

namespace App\Http\Controllers\Doctor;

use App\Exceptions\InvalidStatusTransitionException;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {}

    public function index(Request $request): View
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor, 403);

        $query = $doctor->appointments()->with('patient');

        if ($date = $request->input('date')) {
            $query->whereDate('appointment_date', $date);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time')
            ->paginate(25)
            ->withQueryString();

        return view('doctor.appointments.index', compact('appointments'));
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor && $appointment->doctor_id === $doctor->id, 403);

        $request->validate([
            'status' => 'required|in:in_progress,completed,no_show',
            'reason' => 'nullable|string',
        ]);

        try {
            $this->appointmentService->transition(
                $appointment,
                $request->input('status'),
                $request->input('reason')
            );
        } catch (InvalidStatusTransitionException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Appointment status updated.');
    }
}

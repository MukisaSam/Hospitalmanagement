<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\SlotUnavailableException;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AppointmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function __construct(private AppointmentService $appointmentService) {}

    public function index(Request $request): View
    {
        $query = Appointment::with(['patient', 'doctor', 'doctor.department']);

        if ($date = $request->input('date')) {
            $query->whereDate('appointment_date', $date);
        }

        if ($doctorId = $request->input('doctor_id')) {
            $query->where('doctor_id', $doctorId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'asc')
            ->paginate(25)
            ->withQueryString();

        $doctors = Doctor::where('status', 'active')->orderBy('first_name')->get();

        return view('appointments.index', compact('appointments', 'doctors'));
    }

    public function create(): View
    {
        $doctors  = Doctor::with('department')->where('status', 'active')->orderBy('first_name')->get();
        $patients = Patient::orderBy('first_name')->get();

        return view('appointments.create', compact('doctors', 'patients'));
    }

    public function store(StoreAppointmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        try {
            $this->appointmentService->validateSlot(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time']
            );
        } catch (SlotUnavailableException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $appointment = Appointment::create([
            ...$data,
            'status'    => 'pending',
            'booked_by' => auth()->id(),
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment booked successfully.');
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['patient', 'doctor', 'doctor.department', 'medicalRecord', 'invoice', 'logs.changedBy']);

        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        $doctors  = Doctor::with('department')->where('status', 'active')->orderBy('first_name')->get();
        $patients = Patient::orderBy('first_name')->get();

        return view('appointments.edit', compact('appointment', 'doctors', 'patients'));
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validated();

        $cancellableStatuses = ['pending', 'confirmed'];
        if (!in_array($appointment->status->value, $cancellableStatuses)) {
            return back()->with('error', 'This appointment cannot be rescheduled in its current status.');
        }

        try {
            $this->appointmentService->validateSlot(
                $data['doctor_id'],
                $data['appointment_date'],
                $data['appointment_time'],
                $appointment->id
            );
        } catch (SlotUnavailableException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $appointment->update([
            'patient_id'       => $data['patient_id'],
            'doctor_id'        => $data['doctor_id'],
            'appointment_date' => $data['appointment_date'],
            'appointment_time' => $data['appointment_time'],
            'type'             => $data['type'],
            'notes'            => $data['notes'] ?? null,
        ]);

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment rescheduled successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $cancellableStatuses = ['pending', 'confirmed'];
        if (!in_array($appointment->status->value, $cancellableStatuses)) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Only pending or confirmed appointments can be cancelled.');
        }

        $cancellationReason = request()->input('cancellation_reason', 'Cancelled by staff.');

        try {
            $this->appointmentService->transition($appointment, 'cancelled', $cancellationReason);
        } catch (InvalidStatusTransitionException $e) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    public function updateStatus(Request $request, Appointment $appointment): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string',
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

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment status updated successfully.');
    }
}

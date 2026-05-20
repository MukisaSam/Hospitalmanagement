<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function index(): View
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor, 403);

        $records = MedicalRecord::with(['patient', 'appointment'])
            ->where('doctor_id', $doctor->id)
            ->orderBy('visit_date', 'desc')
            ->paginate(25);

        return view('doctor.medical-records.index', compact('records'));
    }

    public function create(Request $request): View
    {
        $doctor   = auth()->user()->doctor;
        abort_unless($doctor, 403);

        $patients     = Patient::orderBy('first_name')->get();
        $appointmentId = $request->input('appointment_id');
        $appointment   = $appointmentId ? Appointment::find($appointmentId) : null;

        return view('doctor.medical-records.create', compact('patients', 'appointment'));
    }

    public function store(Request $request): RedirectResponse
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor, 403);

        $data = $request->validate([
            'patient_id'      => 'required|integer|exists:patients,id',
            'appointment_id'  => 'nullable|integer|exists:appointments,id',
            'visit_date'      => 'required|date',
            'chief_complaint' => 'required|string',
            'symptoms'        => 'nullable|string',
            'diagnosis'       => 'required|string',
            'diagnosis_code'  => 'nullable|string|max:20',
            'treatment_plan'  => 'nullable|string',
            'notes'           => 'nullable|string',
            'follow_up_date'  => 'nullable|date|after:today',
        ]);

        $data['doctor_id'] = $doctor->id;

        MedicalRecord::create($data);

        return redirect()->route('doctor.appointments.index')
            ->with('success', 'Medical record created successfully.');
    }

    public function show(MedicalRecord $medicalRecord): View
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor && $medicalRecord->doctor_id === $doctor->id, 403);

        $medicalRecord->load(['patient', 'prescriptions', 'vitals', 'appointment']);

        return view('doctor.medical-records.show', compact('medicalRecord'));
    }

    public function edit(MedicalRecord $medicalRecord): View
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor && $medicalRecord->doctor_id === $doctor->id, 403);

        return view('doctor.medical-records.edit', compact('medicalRecord'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor && $medicalRecord->doctor_id === $doctor->id, 403);

        $data = $request->validate([
            'chief_complaint' => 'required|string',
            'symptoms'        => 'nullable|string',
            'diagnosis'       => 'required|string',
            'diagnosis_code'  => 'nullable|string|max:20',
            'treatment_plan'  => 'nullable|string',
            'notes'           => 'nullable|string',
            'follow_up_date'  => 'nullable|date|after:today',
        ]);

        $medicalRecord->update($data);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Medical record updated successfully.');
    }
}

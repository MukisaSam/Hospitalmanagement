<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    private function assertDoctorOwns(MedicalRecord $record): void
    {
        $doctor = auth()->user()->doctor;
        abort_unless($doctor && $record->doctor_id === $doctor->id, 403);
    }

    public function store(Request $request, MedicalRecord $medicalRecord): RedirectResponse
    {
        $this->assertDoctorOwns($medicalRecord);

        $data = $request->validate([
            'medicine_name' => 'required|string|max:200',
            'dosage'        => 'required|string|max:100',
            'frequency'     => 'required|string|max:100',
            'duration'      => 'nullable|string|max:100',
            'instructions'  => 'nullable|string',
        ]);

        $data['medical_record_id'] = $medicalRecord->id;

        Prescription::create($data);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Prescription added successfully.');
    }

    public function edit(MedicalRecord $medicalRecord, Prescription $prescription): View
    {
        $this->assertDoctorOwns($medicalRecord);

        return view('doctor.prescriptions.edit', compact('medicalRecord', 'prescription'));
    }

    public function update(Request $request, MedicalRecord $medicalRecord, Prescription $prescription): RedirectResponse
    {
        $this->assertDoctorOwns($medicalRecord);

        $data = $request->validate([
            'medicine_name' => 'required|string|max:200',
            'dosage'        => 'required|string|max:100',
            'frequency'     => 'required|string|max:100',
            'duration'      => 'nullable|string|max:100',
            'instructions'  => 'nullable|string',
        ]);

        $prescription->update($data);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Prescription updated successfully.');
    }

    public function destroy(MedicalRecord $medicalRecord, Prescription $prescription): RedirectResponse
    {
        $this->assertDoctorOwns($medicalRecord);

        $prescription->delete();

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Prescription deleted successfully.');
    }
}

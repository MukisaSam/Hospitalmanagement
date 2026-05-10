<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use App\Models\Vital;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VitalController extends Controller
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
            'blood_pressure'    => 'nullable|string|max:20',
            'pulse_rate'        => 'nullable|numeric',
            'temperature'       => 'nullable|numeric',
            'weight'            => 'nullable|numeric|min:0',
            'height'            => 'nullable|numeric|min:0',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
        ]);

        // Compute BMI if weight and height are provided
        if (!empty($data['weight']) && !empty($data['height']) && $data['height'] > 0) {
            $heightM = $data['height'] / 100; // cm to m
            $data['bmi'] = round($data['weight'] / ($heightM ** 2), 2);
        }

        $data['medical_record_id'] = $medicalRecord->id;
        $data['recorded_by']       = auth()->id();
        $data['recorded_at']       = now();

        Vital::create($data);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Vitals recorded successfully.');
    }

    public function update(Request $request, MedicalRecord $medicalRecord, Vital $vital): RedirectResponse
    {
        $this->assertDoctorOwns($medicalRecord);

        $data = $request->validate([
            'blood_pressure'    => 'nullable|string|max:20',
            'pulse_rate'        => 'nullable|numeric',
            'temperature'       => 'nullable|numeric',
            'weight'            => 'nullable|numeric|min:0',
            'height'            => 'nullable|numeric|min:0',
            'oxygen_saturation' => 'nullable|numeric|min:0|max:100',
        ]);

        if (!empty($data['weight']) && !empty($data['height']) && $data['height'] > 0) {
            $heightM = $data['height'] / 100;
            $data['bmi'] = round($data['weight'] / ($heightM ** 2), 2);
        }

        $data['recorded_by'] = auth()->id();
        $data['recorded_at'] = now();

        $vital->update($data);

        return redirect()->route('doctor.medical-records.show', $medicalRecord)
            ->with('success', 'Vitals updated successfully.');
    }
}

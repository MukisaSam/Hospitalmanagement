<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Services\SettingService;
use Illuminate\View\View;

class MedicalRecordController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index(): View
    {
        $patient = auth()->user()->patient;
        abort_unless($patient, 403);

        $showNotesToPatient = $this->settingService->get('show_notes_to_patient', false);

        $records = $patient->medicalRecords()
            ->with(['doctor', 'prescriptions', 'vitals'])
            ->orderBy('visit_date', 'desc')
            ->paginate(25);

        return view('patient.medical-records.index', compact('records', 'showNotesToPatient'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Models\User;
use App\Services\MrnGeneratorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function __construct(private MrnGeneratorService $mrnGenerator) {}

    public function index(Request $request): View
    {
        $query = Patient::with('user')->withoutTrashed();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('mrn', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('created_at', 'desc')->paginate(25)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(StorePatientRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['mrn'] = $this->mrnGenerator->generate();

        // Create patient user account if email is provided
        $userId = null;
        if (!empty($data['email'])) {
            $user = User::create([
                'name'     => $data['first_name'] . ' ' . $data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'), // Default password
                'role'     => 'patient',
                'status'   => 'active',
            ]);
            $userId = $user->id;
        }

        $data['user_id'] = $userId;

        Patient::create($data);

        return redirect()->route('patients.index')
            ->with('success', 'Patient registered successfully. MRN: ' . $data['mrn']);
    }

    public function show(Patient $patient): View
    {
        $patient->load([
            'user',
            'appointments' => fn ($q) => $q->with(['doctor', 'doctor.department'])->orderBy('appointment_date', 'desc')->limit(5),
            'medicalRecords' => fn ($q) => $q->with('doctor')->orderBy('visit_date', 'desc')->limit(3),
            'invoices' => fn ($q) => $q->orderBy('created_at', 'desc'),
        ]);

        return view('patients.show', compact('patient'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        // Sync user name/email if a user account exists
        if ($patient->user) {
            $patient->user->update([
                'name'  => $request->input('first_name') . ' ' . $request->input('last_name'),
                'email' => $request->input('email') ?? $patient->user->email,
            ]);
        }

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Patient record updated successfully.');
    }

    public function destroy(Patient $patient): RedirectResponse
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Patient archived successfully.');
    }

    public function archived(): View
    {
        abort_unless(auth()->user()->role->value === 'admin', 403);

        $patients = Patient::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->paginate(25);

        return view('patients.archived', compact('patients'));
    }

    public function restore(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->role->value === 'admin', 403);

        $patient = Patient::onlyTrashed()->findOrFail($id);
        $patient->restore();

        return redirect()->route('patients.archived')
            ->with('success', 'Patient record restored successfully.');
    }
}

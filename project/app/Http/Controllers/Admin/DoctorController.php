<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DoctorController extends Controller
{
    public function index(Request $request): View
    {
        $query = Doctor::with(['user', 'department']);

        if ($departmentId = $request->input('department_id')) {
            $query->where('department_id', $departmentId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('specialization', 'like', "%{$search}%");
            });
        }

        $doctors     = $query->orderBy('first_name')->paginate(25)->withQueryString();
        $departments = Department::orderBy('name')->get();

        return view('admin.doctors.index', compact('doctors', 'departments'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.doctors.create', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|max:255|unique:users,email',
            'department_id'    => 'nullable|integer|exists:departments,id',
            'specialization'   => 'required|string|max:100',
            'qualification'    => 'nullable|string|max:200',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'bio'              => 'nullable|string',
            'phone_number'     => 'nullable|string|max:20',
            'status'           => 'required|in:active,inactive,on_leave',
        ]);

        DB::transaction(function () use ($data) {
            $user = User::create([
                'name'     => $data['first_name'] . ' ' . $data['last_name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
                'role'     => 'doctor',
                'status'   => 'active',
            ]);

            Doctor::create([
                'user_id'          => $user->id,
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'],
                'department_id'    => $data['department_id'],
                'specialization'   => $data['specialization'],
                'qualification'    => $data['qualification'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'consultation_fee' => $data['consultation_fee'],
                'bio'              => $data['bio'] ?? null,
                'phone_number'     => $data['phone_number'] ?? null,
                'status'           => $data['status'],
            ]);
        });

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor created successfully. Default password: password');
    }

    public function show(Doctor $doctor): View
    {
        $doctor->load(['user', 'department', 'schedules']);

        return view('admin.doctors.show', compact('doctor'));
    }

    public function edit(Doctor $doctor): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.doctors.edit', compact('doctor', 'departments'));
    }

    public function update(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'department_id'    => 'nullable|integer|exists:departments,id',
            'specialization'   => 'required|string|max:100',
            'qualification'    => 'nullable|string|max:200',
            'experience_years' => 'nullable|integer|min:0',
            'consultation_fee' => 'required|numeric|min:0',
            'bio'              => 'nullable|string',
            'phone_number'     => 'nullable|string|max:20',
            'status'           => 'required|in:active,inactive,on_leave',
        ]);

        $doctor->update($data);

        if ($doctor->user) {
            $doctor->user->update([
                'name' => $data['first_name'] . ' ' . $data['last_name'],
            ]);
        }

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor updated successfully.');
    }

    public function destroy(Doctor $doctor): RedirectResponse
    {
        $doctor->delete();

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor archived successfully.');
    }
}

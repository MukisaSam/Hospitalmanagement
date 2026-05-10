<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::with('headDoctor')
            ->withCount('doctors')
            ->orderBy('name')
            ->paginate(25);

        return view('admin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        $doctors = Doctor::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.departments.create', compact('doctors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100|unique:departments,name',
            'description'      => 'nullable|string',
            'head_doctor_id'   => 'nullable|integer|exists:doctors,id',
            'location'         => 'nullable|string|max:100',
            'phone_extension'  => 'nullable|string|max:20',
        ]);

        Department::create($data);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function show(Department $department): View
    {
        $department->load(['headDoctor', 'doctors']);

        return view('admin.departments.show', compact('department'));
    }

    public function edit(Department $department): View
    {
        $doctors = Doctor::where('status', 'active')->orderBy('first_name')->get();

        return view('admin.departments.edit', compact('department', 'doctors'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $data = $request->validate([
            'name'            => 'required|string|max:100|unique:departments,name,' . $department->id,
            'description'     => 'nullable|string',
            'head_doctor_id'  => 'nullable|integer|exists:doctors,id',
            'location'        => 'nullable|string|max:100',
            'phone_extension' => 'nullable|string|max:20',
        ]);

        $department->update($data);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // Cannot delete if it has active doctors
        if ($department->doctors()->where('status', 'active')->exists()) {
            return redirect()->route('admin.departments.index')
                ->with('error', 'Cannot delete a department that has active doctors. Reassign or deactivate doctors first.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department archived successfully.');
    }

    public function archived(): View
    {
        $departments = Department::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(25);

        return view('admin.departments.archived', compact('departments'));
    }

    public function restore(int $id): RedirectResponse
    {
        $department = Department::onlyTrashed()->findOrFail($id);
        $department->restore();

        return redirect()->route('admin.departments.archived')
            ->with('success', 'Department restored successfully.');
    }
}

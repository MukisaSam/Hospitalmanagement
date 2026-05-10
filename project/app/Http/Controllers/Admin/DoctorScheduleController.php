<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorScheduleController extends Controller
{
    public function index(Doctor $doctor): View
    {
        $doctor->load('schedules');

        return view('admin.doctors.schedules.index', compact('doctor'));
    }

    public function create(Doctor $doctor): View
    {
        $existingDays = $doctor->schedules->pluck('day_of_week')->map->value->toArray();

        return view('admin.doctors.schedules.create', compact('doctor', 'existingDays'));
    }

    public function store(Request $request, Doctor $doctor): RedirectResponse
    {
        $data = $request->validate([
            'day_of_week'      => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'required|integer|min:1|max:100',
            'is_available'     => 'boolean',
        ]);

        $data['doctor_id']    = $doctor->id;
        $data['is_available'] = $request->boolean('is_available', true);

        DoctorSchedule::create($data);

        return redirect()->route('admin.doctors.schedules.index', $doctor)
            ->with('success', 'Schedule added successfully.');
    }

    public function edit(Doctor $doctor, DoctorSchedule $schedule): View
    {
        return view('admin.doctors.schedules.edit', compact('doctor', 'schedule'));
    }

    public function update(Request $request, Doctor $doctor, DoctorSchedule $schedule): RedirectResponse
    {
        $data = $request->validate([
            'start_time'       => 'required|date_format:H:i',
            'end_time'         => 'required|date_format:H:i|after:start_time',
            'max_appointments' => 'required|integer|min:1|max:100',
            'is_available'     => 'boolean',
        ]);

        $data['is_available'] = $request->boolean('is_available', false);
        $schedule->update($data);

        return redirect()->route('admin.doctors.schedules.index', $doctor)
            ->with('success', 'Schedule updated successfully.');
    }

    public function destroy(Doctor $doctor, DoctorSchedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('admin.doctors.schedules.index', $doctor)
            ->with('success', 'Schedule removed successfully.');
    }
}

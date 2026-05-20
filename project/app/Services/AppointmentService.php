<?php

namespace App\Services;

use App\Exceptions\InvalidStatusTransitionException;
use App\Exceptions\SlotUnavailableException;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    private static array $transitions = [
        'pending'     => ['confirmed', 'cancelled'],
        'confirmed'   => ['checked_in', 'cancelled', 'no_show'],
        'checked_in'  => ['in_progress', 'no_show'],
        'in_progress' => ['completed'],
        'completed'   => [],
        'cancelled'   => [],
        'no_show'     => [],
    ];

    public function __construct(private InvoiceService $invoiceService) {}

    public function validateSlot(int $doctorId, string $date, string $time, ?int $excludeAppointmentId = null): void
    {
        if ($date < now()->toDateString()) {
            throw new SlotUnavailableException('Appointment date cannot be in the past.');
        }

        $dayOfWeek = strtolower(date('l', strtotime($date)));

        $schedule = DoctorSchedule::where('doctor_id', $doctorId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (!$schedule) {
            throw new SlotUnavailableException('Doctor is not available on this day.');
        }

        if ($time < $schedule->start_time || $time > $schedule->end_time) {
            throw new SlotUnavailableException('Selected time is outside doctor\'s working hours.');
        }

        $query = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_date', $date)
            ->whereIn('status', ['confirmed', 'pending', 'checked_in', 'in_progress']);

        if ($excludeAppointmentId) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        if ($query->count() >= $schedule->max_appointments) {
            throw new SlotUnavailableException('Doctor\'s appointment slots for this day are full.');
        }
    }

    public function transition(Appointment $appointment, string $newStatus, ?string $reason = null): void
    {
        $oldStatus = $appointment->status->value;
        $allowed = static::$transitions[$oldStatus] ?? [];

        if (!in_array($newStatus, $allowed)) {
            throw new InvalidStatusTransitionException("Cannot transition from {$oldStatus} to {$newStatus}.");
        }

        if (in_array($newStatus, ['checked_in', 'in_progress', 'completed'], true)) {
            $appointment->loadMissing('invoice');
            if (!$appointment->invoice || $appointment->invoice->status->value !== 'paid') {
                throw new InvalidStatusTransitionException('Payment must be confirmed before this appointment can proceed.');
            }
        }

        DB::transaction(function () use ($appointment, $oldStatus, $newStatus, $reason) {
            AppointmentLog::create([
                'appointment_id' => $appointment->id,
                'old_status'     => $oldStatus,
                'new_status'     => $newStatus,
                'changed_by'     => auth()->id(),
                'reason'         => $reason,
            ]);

            $appointment->update(['status' => $newStatus]);

            if ($newStatus === 'in_progress') {
                MedicalRecord::firstOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'patient_id'      => $appointment->patient_id,
                        'doctor_id'       => $appointment->doctor_id,
                        'visit_date'      => today(),
                        'chief_complaint' => '',
                        'diagnosis'       => '',
                    ]
                );
            }

            if ($newStatus === 'confirmed') {
                $this->invoiceService->createForAppointment($appointment);
            }

            if ($newStatus === 'completed') {
                $this->invoiceService->createForAppointment($appointment);
            }
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $patients = Patient::all();
        $doctors  = Doctor::with('user')->get();

        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->warn('AppointmentSeeder: patients or doctors table is empty — skipping.');
            return;
        }

        // The receptionist user books most appointments
        $receptionist = User::where('email', 'receptionist@hms.com')->first();
        $adminUser    = User::where('email', 'admin@hms.com')->first();
        $bookedBy     = $receptionist?->id ?? $adminUser?->id;

        // Appointment definitions — mix of past and future, various statuses and types
        $definitions = [
            // ---- COMPLETED (past) — will also get Invoice + MedicalRecord ----
            [
                'patient_index' => 0,
                'doctor_index'  => 0,
                'date'          => '2026-04-07',
                'time'          => '09:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Routine cardiac check-up.',
            ],
            [
                'patient_index' => 1,
                'doctor_index'  => 1,
                'date'          => '2026-04-08',
                'time'          => '10:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Headache evaluation and neurological assessment.',
            ],
            [
                'patient_index' => 2,
                'doctor_index'  => 2,
                'date'          => '2026-04-09',
                'time'          => '11:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Knee pain follow-up after previous X-ray.',
            ],
            [
                'patient_index' => 3,
                'doctor_index'  => 3,
                'date'          => '2026-04-10',
                'time'          => '08:30:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Childhood immunisation review.',
            ],
            [
                'patient_index' => 4,
                'doctor_index'  => 4,
                'date'          => '2026-04-11',
                'time'          => '14:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'General health screening.',
            ],
            [
                'patient_index' => 5,
                'doctor_index'  => 0,
                'date'          => '2026-04-14',
                'time'          => '09:30:00',
                'type'          => 'follow_up',
                'status'        => 'completed',
                'notes'         => 'Follow-up after cardiac medication adjustment.',
            ],
            [
                'patient_index' => 6,
                'doctor_index'  => 1,
                'date'          => '2026-04-15',
                'time'          => '11:30:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Epilepsy management review.',
            ],
            [
                'patient_index' => 7,
                'doctor_index'  => 2,
                'date'          => '2026-04-16',
                'time'          => '13:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Back pain consultation.',
            ],
            [
                'patient_index' => 8,
                'doctor_index'  => 3,
                'date'          => '2026-04-17',
                'time'          => '10:30:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Paediatric growth assessment.',
            ],
            [
                'patient_index' => 9,
                'doctor_index'  => 4,
                'date'          => '2026-04-22',
                'time'          => '15:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Diabetes management and blood sugar review.',
            ],
            [
                'patient_index' => 10,
                'doctor_index'  => 0,
                'date'          => '2026-04-23',
                'time'          => '09:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Hypertension check and medication renewal.',
            ],
            [
                'patient_index' => 11,
                'doctor_index'  => 4,
                'date'          => '2026-04-24',
                'time'          => '11:00:00',
                'type'          => 'follow_up',
                'status'        => 'completed',
                'notes'         => 'Follow-up after flu treatment.',
            ],

            // ---- CANCELLED (past) ----
            [
                'patient_index' => 12,
                'doctor_index'  => 1,
                'date'          => '2026-04-21',
                'time'          => '10:00:00',
                'type'          => 'opd',
                'status'        => 'cancelled',
                'notes'         => 'Patient requested cancellation.',
                'cancellation_reason' => 'Patient travelled out of town unexpectedly.',
            ],
            [
                'patient_index' => 13,
                'doctor_index'  => 2,
                'date'          => '2026-04-28',
                'time'          => '14:00:00',
                'type'          => 'opd',
                'status'        => 'cancelled',
                'notes'         => null,
                'cancellation_reason' => 'Doctor was unavailable due to emergency surgery.',
            ],

            // ---- NO_SHOW (past) ----
            [
                'patient_index' => 14,
                'doctor_index'  => 3,
                'date'          => '2026-04-29',
                'time'          => '09:00:00',
                'type'          => 'opd',
                'status'        => 'no_show',
                'notes'         => 'Patient did not arrive.',
            ],

            // ---- CONFIRMED (future) ----
            [
                'patient_index' => 0,
                'doctor_index'  => 0,
                'date'          => '2026-05-12',
                'time'          => '09:00:00',
                'type'          => 'follow_up',
                'status'        => 'confirmed',
                'notes'         => 'Quarterly cardiac review.',
            ],
            [
                'patient_index' => 1,
                'doctor_index'  => 1,
                'date'          => '2026-05-13',
                'time'          => '10:00:00',
                'type'          => 'opd',
                'status'        => 'confirmed',
                'notes'         => 'MRI result discussion.',
            ],
            [
                'patient_index' => 2,
                'doctor_index'  => 2,
                'date'          => '2026-05-14',
                'time'          => '11:00:00',
                'type'          => 'follow_up',
                'status'        => 'confirmed',
                'notes'         => 'Post-physiotherapy review.',
            ],
            [
                'patient_index' => 5,
                'doctor_index'  => 4,
                'date'          => '2026-05-15',
                'time'          => '14:00:00',
                'type'          => 'opd',
                'status'        => 'confirmed',
                'notes'         => 'Blood pressure monitoring.',
            ],
            [
                'patient_index' => 6,
                'doctor_index'  => 3,
                'date'          => '2026-05-16',
                'time'          => '09:30:00',
                'type'          => 'opd',
                'status'        => 'confirmed',
                'notes'         => 'Well-child visit.',
            ],

            // ---- PENDING (future) ----
            [
                'patient_index' => 15,
                'doctor_index'  => 0,
                'date'          => '2026-05-19',
                'time'          => '10:00:00',
                'type'          => 'opd',
                'status'        => 'pending',
                'notes'         => 'New patient — chest pain evaluation.',
            ],
            [
                'patient_index' => 16,
                'doctor_index'  => 1,
                'date'          => '2026-05-20',
                'time'          => '11:00:00',
                'type'          => 'opd',
                'status'        => 'pending',
                'notes'         => 'Dizziness and balance issues.',
            ],
            [
                'patient_index' => 17,
                'doctor_index'  => 2,
                'date'          => '2026-05-21',
                'time'          => '13:00:00',
                'type'          => 'opd',
                'status'        => 'pending',
                'notes'         => 'Sports injury assessment.',
            ],
            [
                'patient_index' => 18,
                'doctor_index'  => 4,
                'date'          => '2026-05-22',
                'time'          => '08:30:00',
                'type'          => 'opd',
                'status'        => 'pending',
                'notes'         => 'General wellness check.',
            ],
            [
                'patient_index' => 19,
                'doctor_index'  => 3,
                'date'          => '2026-05-23',
                'time'          => '15:00:00',
                'type'          => 'opd',
                'status'        => 'pending',
                'notes'         => 'Child fever evaluation.',
            ],

            // ---- CHECKED_IN (today-ish) ----
            [
                'patient_index' => 7,
                'doctor_index'  => 0,
                'date'          => '2026-05-09',
                'time'          => '08:00:00',
                'type'          => 'emergency',
                'status'        => 'checked_in',
                'notes'         => 'Patient arrived with acute chest pain.',
            ],

            // ---- IN_PROGRESS (today) ----
            [
                'patient_index' => 8,
                'doctor_index'  => 1,
                'date'          => '2026-05-09',
                'time'          => '09:00:00',
                'type'          => 'opd',
                'status'        => 'in_progress',
                'notes'         => 'Consultation in progress.',
            ],

            // ---- Additional COMPLETED for revenue variety ----
            [
                'patient_index' => 3,
                'doctor_index'  => 4,
                'date'          => '2026-03-10',
                'time'          => '10:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Malaria treatment follow-up.',
            ],
            [
                'patient_index' => 4,
                'doctor_index'  => 2,
                'date'          => '2026-03-15',
                'time'          => '11:00:00',
                'type'          => 'opd',
                'status'        => 'completed',
                'notes'         => 'Fracture recovery assessment.',
            ],
            [
                'patient_index' => 9,
                'doctor_index'  => 0,
                'date'          => '2026-03-20',
                'time'          => '14:00:00',
                'type'          => 'ipd',
                'status'        => 'completed',
                'notes'         => 'Post-admission cardiac review.',
            ],
        ];

        $invoiceSequence = 1;
        $invoiceDatePrefix = '20260509';

        foreach ($definitions as $def) {
            $patient = $patients->get($def['patient_index']);
            $doctor  = $doctors->get($def['doctor_index']);

            if (!$patient || !$doctor) {
                continue;
            }

            // Avoid duplicate appointments for the same patient+doctor+date+time
            $appointment = Appointment::firstOrCreate(
                [
                    'patient_id'       => $patient->id,
                    'doctor_id'        => $doctor->id,
                    'appointment_date' => $def['date'],
                    'appointment_time' => $def['time'],
                ],
                [
                    'type'                => $def['type'],
                    'status'              => $def['status'],
                    'notes'               => $def['notes'] ?? null,
                    'cancellation_reason' => $def['cancellation_reason'] ?? null,
                    'booked_by'           => $bookedBy,
                ]
            );

            // For completed appointments create MedicalRecord + Invoice if they don't already exist
            if ($def['status'] === 'completed') {
                MedicalRecord::firstOrCreate(
                    ['appointment_id' => $appointment->id],
                    [
                        'patient_id'     => $patient->id,
                        'doctor_id'      => $doctor->id,
                        'visit_date'     => $def['date'],
                        'chief_complaint' => $def['notes'] ?? 'General consultation',
                        'diagnosis'      => 'Assessment completed by attending physician.',
                        'treatment_plan' => 'Treatment plan discussed with patient.',
                    ]
                );

                if (!Invoice::where('appointment_id', $appointment->id)->exists()) {
                    $consultationFee = (float) $doctor->consultation_fee;
                    $taxRate = 18.00;
                    $subtotal    = $consultationFee;
                    $taxAmount   = round($subtotal * ($taxRate / 100), 2);
                    $totalAmount = round($subtotal + $taxAmount, 2);
                    $invoiceNumber = sprintf('INV-%s-%04d', $invoiceDatePrefix, $invoiceSequence++);

                    DB::transaction(function () use (
                        $appointment, $patient, $doctor, $adminUser,
                        $invoiceNumber, $subtotal, $taxRate, $taxAmount, $totalAmount, $consultationFee
                    ) {
                        $invoice = Invoice::create([
                            'invoice_number'  => $invoiceNumber,
                            'patient_id'      => $patient->id,
                            'appointment_id'  => $appointment->id,
                            'doctor_id'       => $doctor->id,
                            'subtotal'        => $subtotal,
                            'tax_rate'        => $taxRate,
                            'tax_amount'      => $taxAmount,
                            'discount_amount' => 0.00,
                            'total_amount'    => $totalAmount,
                            'amount_paid'     => $totalAmount, // marked as paid
                            'status'          => 'paid',
                            'due_date'        => now()->addDays(30)->toDateString(),
                            'created_by'      => $adminUser?->id,
                        ]);

                        InvoiceItem::create([
                            'invoice_id'  => $invoice->id,
                            'description' => 'Consultation fee — ' . $doctor->specialization,
                            'quantity'    => 1,
                            'unit_price'  => $consultationFee,
                            'total_price' => $consultationFee,
                        ]);
                    });
                }
            }
        }
    }
}

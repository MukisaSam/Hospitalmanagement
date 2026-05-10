<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctors = [
            [
                'user' => [
                    'name'   => 'Dr. Sarah Johnson',
                    'email'  => 'sarah.johnson@hms.com',
                ],
                'doctor' => [
                    'first_name'       => 'Sarah',
                    'last_name'        => 'Johnson',
                    'specialization'   => 'Cardiologist',
                    'qualification'    => 'MBChB, MMed (Cardiology)',
                    'experience_years' => 12,
                    'consultation_fee' => 50000.00,
                    'bio'              => 'Dr. Sarah Johnson is an experienced cardiologist with over 12 years of practice in interventional cardiology.',
                    'phone_number'     => '+256-701-000-101',
                    'status'           => 'active',
                ],
                'department' => 'Cardiology',
            ],
            [
                'user' => [
                    'name'   => 'Dr. Michael Ochieng',
                    'email'  => 'michael.ochieng@hms.com',
                ],
                'doctor' => [
                    'first_name'       => 'Michael',
                    'last_name'        => 'Ochieng',
                    'specialization'   => 'Neurologist',
                    'qualification'    => 'MBChB, MMed (Neurology), PhD',
                    'experience_years' => 9,
                    'consultation_fee' => 55000.00,
                    'bio'              => 'Dr. Michael Ochieng specialises in neurodegenerative disorders and stroke management.',
                    'phone_number'     => '+256-702-000-102',
                    'status'           => 'active',
                ],
                'department' => 'Neurology',
            ],
            [
                'user' => [
                    'name'   => 'Dr. Grace Nakato',
                    'email'  => 'grace.nakato@hms.com',
                ],
                'doctor' => [
                    'first_name'       => 'Grace',
                    'last_name'        => 'Nakato',
                    'specialization'   => 'Orthopedic Surgeon',
                    'qualification'    => 'MBChB, MMed (Orthopedics), FCOS',
                    'experience_years' => 15,
                    'consultation_fee' => 60000.00,
                    'bio'              => 'Dr. Grace Nakato is a senior orthopedic surgeon with expertise in joint replacement and sports injuries.',
                    'phone_number'     => '+256-703-000-103',
                    'status'           => 'active',
                ],
                'department' => 'Orthopedics',
            ],
            [
                'user' => [
                    'name'   => 'Dr. James Ssemanda',
                    'email'  => 'james.ssemanda@hms.com',
                ],
                'doctor' => [
                    'first_name'       => 'James',
                    'last_name'        => 'Ssemanda',
                    'specialization'   => 'Pediatrician',
                    'qualification'    => 'MBChB, MMed (Paediatrics)',
                    'experience_years' => 7,
                    'consultation_fee' => 40000.00,
                    'bio'              => 'Dr. James Ssemanda is a dedicated paediatrician focused on child health and development.',
                    'phone_number'     => '+256-704-000-104',
                    'status'           => 'active',
                ],
                'department' => 'Pediatrics',
            ],
            [
                'user' => [
                    'name'   => 'Dr. Amina Nalwanga',
                    'email'  => 'amina.nalwanga@hms.com',
                ],
                'doctor' => [
                    'first_name'       => 'Amina',
                    'last_name'        => 'Nalwanga',
                    'specialization'   => 'General Practitioner',
                    'qualification'    => 'MBChB, Diploma in Family Medicine',
                    'experience_years' => 5,
                    'consultation_fee' => 30000.00,
                    'bio'              => 'Dr. Amina Nalwanga provides comprehensive primary care and chronic disease management for adults.',
                    'phone_number'     => '+256-705-000-105',
                    'status'           => 'active',
                ],
                'department' => 'General Medicine',
            ],
        ];

        $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];

        foreach ($doctors as $data) {
            $department = Department::where('name', $data['department'])->first();

            $user = User::firstOrCreate(
                ['email' => $data['user']['email']],
                [
                    'name'     => $data['user']['name'],
                    'password' => Hash::make('Doctor@123'),
                    'role'     => 'doctor',
                    'status'   => 'active',
                ]
            );

            $doctor = Doctor::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($data['doctor'], [
                    'department_id' => $department?->id,
                ])
            );

            foreach ($weekdays as $day) {
                DoctorSchedule::firstOrCreate(
                    [
                        'doctor_id'  => $doctor->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'start_time'       => '08:00:00',
                        'end_time'         => '17:00:00',
                        'max_appointments' => 20,
                        'is_available'     => true,
                    ]
                );
            }
        }
    }
}

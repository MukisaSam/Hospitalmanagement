<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name'        => 'Cardiology',
                'description' => 'Specialises in diagnosing and treating diseases and conditions of the heart and blood vessels.',
                'location'    => 'Block A, Ground Floor',
                'phone_extension' => '101',
            ],
            [
                'name'        => 'Neurology',
                'description' => 'Deals with disorders of the nervous system, including the brain, spinal cord, and peripheral nerves.',
                'location'    => 'Block A, First Floor',
                'phone_extension' => '102',
            ],
            [
                'name'        => 'Orthopedics',
                'description' => 'Focuses on conditions involving the musculoskeletal system — bones, joints, ligaments, tendons, and muscles.',
                'location'    => 'Block B, Ground Floor',
                'phone_extension' => '103',
            ],
            [
                'name'        => 'Pediatrics',
                'description' => 'Provides medical care for infants, children, and adolescents up to the age of 18.',
                'location'    => 'Block B, First Floor',
                'phone_extension' => '104',
            ],
            [
                'name'        => 'General Medicine',
                'description' => 'Handles the prevention, diagnosis, and non-surgical treatment of a wide range of adult diseases.',
                'location'    => 'Block C, Ground Floor',
                'phone_extension' => '105',
            ],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(
                ['name' => $dept['name']],
                [
                    'description'     => $dept['description'],
                    'location'        => $dept['location'],
                    'phone_extension' => $dept['phone_extension'],
                ]
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Seeders are called in dependency order:
     *  settings → departments → doctors (needs departments)
     *  → users (receptionist) → patients → appointments (needs patients + doctors)
     *  → admin
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            DepartmentSeeder::class,
            DoctorSeeder::class,
            UserSeeder::class,
            PatientSeeder::class,
            AppointmentSeeder::class,
            AdminSeeder::class,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'receptionist@hms.com'],
            [
                'name'     => 'Jane Receptionist',
                'password' => Hash::make('Recep@123'),
                'role'     => 'receptionist',
                'status'   => 'active',
            ]
        );
    }
}

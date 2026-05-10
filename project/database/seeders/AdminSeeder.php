<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@hms.com'],
            [
                'name'     => 'System Administrator',
                'password' => Hash::make('Admin@123'),
                'role'     => 'admin',
                'status'   => 'active',
            ]
        );
    }
}

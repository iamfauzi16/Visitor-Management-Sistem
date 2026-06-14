<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'              => 'Admin VMS',
                'email'             => 'admin@vms.test',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name'              => 'Resepsionis VMS',
                'email'             => 'resepsionis@vms.test',
                'password'          => Hash::make('password'),
                'role'              => 'receptionist',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(['email' => $data['email']], $data);
        }
    }
}

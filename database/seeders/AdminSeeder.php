<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sekolah.local'],
            [
                'name' => 'Admin Sekolah',
                'nisn' => null,
                'nip' => null,
                'kelas' => null,
                'phone' => '081200000001',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
                'is_verified' => true,
            ]
        );
    }
}

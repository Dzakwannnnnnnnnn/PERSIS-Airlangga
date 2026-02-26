<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator E-Izin',
            'email' => 'admin@izin.com', // Email untuk login admin
            'nisn' => '0000000000',      // NISN dummy untuk admin
            'phone' => '08123456789',
            'password' => Hash::make('admin123'), // Password untuk login admin
            'role' => 'admin',           // Pastikan role-nya admin
            'is_verified' => true,       // Admin otomatis terverifikasi
        ]);
    }
}
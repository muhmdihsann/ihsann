<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Tambahkan ini untuk hashing password

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Membuat akun khusus untuk Operator
        User::create([
            'name'     => 'Operator SPM',
            'email'    => 'operator@spm.go.id',
            'password' => Hash::make('password123'), // Password default: password123
            'role'     => 'operator',
        ]);
    }
}

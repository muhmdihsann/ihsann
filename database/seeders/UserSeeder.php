<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Operator SPM',
            'email' => 'operator@spm.go.id',
            'password' => Hash::make('password123'),
            'role' => 'operator'
        ]);
    }
}

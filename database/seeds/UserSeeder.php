<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Admin
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'status' => 'Approved',
                'otp' => Str::random(6),
            ]
        );

        // Seed Borrower
        User::firstOrCreate(
            ['email' => 'borrower@example.com'],
            [
                'name' => 'Borrower User',
                'password' => Hash::make('password'),
                'role' => 'Borrower',
                'status' => 'Pending',
                'otp' => Str::random(6)
            ]
        );

        // Seed Lender
        User::firstOrCreate(
            ['email' => 'lender@example.com'],
            [
                'name' => 'Lender User',
                'password' => Hash::make('password'),
                'role' => 'Lender',
                'status' => 'Approved',
                'otp' => Str::random(6)
            ]
        );
    }
}

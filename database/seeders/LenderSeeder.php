<?php

namespace Database\Seeders;

use App\Models\Lender;
use App\Models\LenderBalance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LenderSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 5) as $i) {
            $lender = Lender::create([
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => fake()->unique()->safeEmail,
                'phone_number' => fake()->phoneNumber,
                'password' => Hash::make(12345678),
                'status' => 'Active',
                'approval_status' => 'Approved',
                'country' => 'Cambodia',
                'Province' => 'Takoe',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            LenderBalance::create([
                'LenderID' => $lender -> id,
                'balance' => fake()->randomFloat(2,1000,2000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

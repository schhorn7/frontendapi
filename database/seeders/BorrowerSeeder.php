<?php

namespace Database\Seeders;

use App\Models\Borrower;
use App\Models\BorrowerBalance;
use App\Models\Loan;
use App\Models\request_loan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use function Symfony\Component\Clock\now;

class BorrowerSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(1, 5) as $i) {
            $borrower = Borrower::create([
                'first_name' => fake()->firstName,
                'last_name' => fake()->lastName,
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make(12345678),
                'status' => 'Active',
                'phone_number' => fake()->phoneNumber,
                'income' => fake()->randomFloat(2, 1000, 30000),
                'employment_status' => 'full-time',
                'approval_status' => 'Approved',
                'identity_path' => '/storage/uploads/kJs68eI60Yz6T4mqpRkQtyeF8HICxg9ZAORCz5kf.jpg',
                'employment_path' => '/storage/uploads/oEXwrIyAYI6W4Ecojf1iifxCGqjnSUOwFsKUyPmF.png',
                'profile_picture' => '/storage/uploads/OmzyXJXq89m61J9ojcAc3umRXwzslTUkFYkG8Fb4.jpg',
                'country' => 'Cambodia',
                'province' => 'Kandal',
                // 'balance' => fake()->randomFloat(2, 0, 5000),
                // 'amount_to_pay' => fake()->randomFloat(2, 1000, 30000),
                'credit_score' => fake()->numberBetween(75, 100),
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            BorrowerBalance::create ([
                'borrowerID' => $borrower->id,
                'balance' => fake()->randomFloat(2,500,1000),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $loan_request = request_loan::create ([
                'request_amount' => 1000,
                'request_duration'=> 30,
                'request_reason' => 'car repair',
                'interest_rate' => 5,
                'total' => 1050,
                'status' => 'Pending',
                'created_at'=> now(),
                'updated_at' => now(),
                'BorrowerID' => $borrower->id,
            ]);

            Loan::create([
                'BorrowerID' => $borrower->id,
                'request_id' => $loan_request->id,
                'request_duration' => 30,
                'request_reason'=> 'car repair',
                'request_amount' => 1000,
                'interest_rate' => 5,
                'total' => 1050,
                'status' => 'Active',
            ]);
        }
    }
}

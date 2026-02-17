<?php
namespace Database\Seeders;
use App\Models\Loan;
use App\Models\Borrower;
use App\Models\Lender;
use App\Models\request_loan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $borrowers = Borrower::all();
        $lenders = Lender::all();

        if ($borrowers->isEmpty() || $lenders->isEmpty()) {
            $this->command->warn('Borrowers or Lenders table is empty.');
            return;
        }

        foreach (range(1, 20) as $i) {
            $status = fake()->randomElement(['pending', 'active', 'completed']);
            $approvedAt = in_array($status, ['active', 'completed']) ? now()->subDays(rand(1, 10)) : null;
            $completedAt = $status === 'completed' ? now()->subDays(rand(0, 5)) : null;

            // Loan::create([
            //     'borrower_id' => $borrowers->random()->id,
            //     'lender_id' => $lenders->random()->id,
            //     'amount' => fake()->randomFloat(2, 1000, 30000),
            //     'interest_rate' => fake()->randomFloat(2, 1.5, 10.0),
            //     'payment_date' => fake()->date('m/d'),
            //     'status' => $status,
            //     'approved_at' => $approvedAt,
            //     'completed_at' => $completedAt,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ]);

            request_loan::create([
                'request_duration' => rand(1, 6),
                'request_reason' => fake()->sentence,
                'request_amount' => fake()->randomFloat(2, 500, 5000),
                'interest_rate' => fake()->randomFloat(1, 2.5, 6.5),
                'income' => fake()->randomFloat(2, 300, 3000),
                'employment_status' => fake()->randomElement(['full-time', 'part-time', 'freelance']),
                'identity_path' => '/storage/uploads/fake_identity.jpg',
                'employment_path' => '/storage/uploads/fake_employment.jpg',
                'BorrowerID' => $borrowers->random()->id,
                'status' => $status,
                'approved_at' => $approvedAt,
                'completed_at' => $completedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}


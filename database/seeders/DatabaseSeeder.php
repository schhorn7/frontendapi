<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\LenderSeeder;
use Database\Seeders\BorrowerSeeder;
use Database\Seeders\LoanSeeder;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {

        // $this->call([
        //     UserSeeder::class,
        // ]);
        $this->call([
            BorrowerSeeder::class,
            LenderSeeder::class,
            //LoanSeeder::class,
        ]);


    }
}

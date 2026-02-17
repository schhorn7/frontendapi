<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id('request_id');
            // $table->foreignId('borrower_id')->constrained()->onDelete('cascade');
            // $table->string('request_duration');
            // $table->double('request_amount');
            // $table->string('request_reason');
            // $table->enum('status', ['pending', 'active', 'completed'])->default('pending');

            // $table->timestamps();
            $table->double('request_amount');
            $table->integer('request_duration')->nullable();
            $table->string('request_reason');
            $table->float('interest_rate');
            $table->double('total', 10, 2);
            // $table->string ('employment_status');
            // $table->double('income');
            // $table->string('identity_path');
            // $table->string('employment_path');
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Active'])->default('pending')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps(); // includes created_at and updated_at

            $table->unsignedBigInteger('BorrowerID');
            $table->foreign('BorrowerID')->references('id')->on('borrowers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

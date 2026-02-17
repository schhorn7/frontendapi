<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Loans table
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('BorrowerID');
            $table->foreign('BorrowerID')->references('id')->on('borrowers')->onDelete('cascade');

            $table->unsignedBigInteger('request_id')->nullable();
            $table->foreign('request_id')->references('request_id')->on('loan_requests')->onDelete('cascade');
            $table->integer('request_duration')->nullable();
            $table->text('request_reason');
            $table->double('request_amount');
            $table->float('interest_rate')->nullable();
            $table->double('total', 10,2);
            $table->enum('status', ['Pending', 'Active', 'Completed', 'Funded'])->default('Pending')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public
    function down(): void
    {
        Schema::dropIfExists('loan_after_approves');
        Schema::dropIfExists('loans');
    }
};

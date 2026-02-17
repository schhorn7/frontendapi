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
        Schema::create('borrower_transactions', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('BorrowerID');
            $table->enum('type', ['credit', 'debit']);
            $table->double('amount');
            $table->string('description')->nullable();
            $table->timestamps();

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

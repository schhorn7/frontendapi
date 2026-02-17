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
       Schema::create('borrowers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('otp')->unique()->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->string('password');
            $table->double('income')->nullable();
            $table->string('phone_number');
            $table->integer('credit_score');
            $table->string('identity_path');
            $table->string('employment_path');
            $table->enum('employment_status', ['full-time', 'part-time']);
            $table->enum('status', ['Inactive', 'Active', 'Suspended'])->default('Inactive');
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('profile_picture')->nullable();
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->timestamps();
        });

        Schema::create('borrowerbalance', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('borrowerID');
            $table->double('balance');
            $table->timestamps();

            //foreign key here :
            $table->foreign('borrowerID')->references('id')->on('borrowers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};

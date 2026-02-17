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
       Schema::create('lenders', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('otp')->unique()->nullable();
            $table->boolean('otp_verified')->default(false);
            $table->string('password');
            $table->string('phone_number');
            
            $table->string('profile_picture')->nullable();
            $table->enum('status', ['Suspended', 'Active', 'Inactive'])->default('Inactive');
            $table->enum('approval_status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->string('country')->nullable();
            $table->string('province')->nullable();
            $table->timestamps();

        });
        Schema::create('lenderbalance', function(Blueprint $table){
            $table->id();
            $table->unsignedBigInteger('LenderID');
            $table->double('balance');
            $table->timestamps();

            //foreign key here :
            $table->foreign('LenderID')->references('id')->on('lenders')->onDelete('cascade');
        });

    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lenders');
        Schema::dropIfExists('LenderBalance');
    }
};

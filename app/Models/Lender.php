<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Lender extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'otp',
        // 'amount',
        // 'credit_score',
        // 'document',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Add any lender-specific relationships or methods here
    public function LenderBalance() {
        return $this->hasOne(LenderBalance::class);
    }

    public function loan() {
        return $this->hasMany(Loan::class);
    }

    public function loan_after_approve() {
        return $this->hasMany(loan_after_approve::class);
    }

    public function transaction() {
        return $this->hasMany(transcation::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Borrower extends Model
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone_number',
        'otp',
        'income',
        'credit_score',
        'status',
        'identity_path',
        'employment_path',
        'employment_status',
        

    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Add any borrower-specific relationships or methods here

    public function BorrowerBalance() {
        return $this->hasOne(BorrowerBalance::class);

    }
    public function loan() {
        return $this->hasMany(Loan::class);
    }
    // public function loan_after_approve() {
    //     return $this->belongsTo(loan_after_approve::class);
    // }
    public function loan_after_approve() {
        return $this->hasMany(loan_after_approve::class);
    }

    public function transaction() {
        return $this->hasMany(transcation::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class loan_after_approve extends Model
{
    protected $fillable = [
        'duration',
        'reason',
        'amount',
        'interest_rate',
        'BorrowerID',
        'LenderID',
        'start_date',
        'payment_date',
        'last_penalized_day',
        'lateDay',
        'income',
        'total',
        'employment_status',
        'status',

    ];
    // public function Borrower() {
    //     return $this->hasOne(Borrower::class);
    // }
    // public function Lender() {
    //     return $this->hasOne(Lender::class);
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'BorrowerID');
    }

    public function lender()
    {
        return $this->belongsTo(Lender::class, 'LenderID');
    }

}

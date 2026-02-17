<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class request_loan extends Model
{
    protected $table = 'loan_requests';
    protected $id= 'request_id';
    use HasFactory;

    protected $fillable = [
        'request_id',
        'request_duration',
        'request_reason',
        'request_amount',
        'interest_rate',
        'total',
        'BorrowerID',
    ];




    // public function loans()
    // {
    //     return $this->hasMany(Loan::class);
    // }
    // public function borrower()
    // {
    //     return $this->belongsTo(Borrower::class, 'BorrowerID'); // Fixed: should be belongsTo, not hasMany
    // }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    public function lender()
    {
        return $this->belongsTo(Lender::class);
    }
}

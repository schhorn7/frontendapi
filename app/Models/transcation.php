<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class transcation extends Model
{
    // public function borrower()
    //     {
    //     return $this->hasMany(Borrower::class);
    // }
    // public function lender()
    //     {
    //     return $this->hasMany(Lender::class);
    //}

    protected $table='transactions';
    protected $primaryKey='id';

    protected $fillable = [
        'LenderID',
        'BorrowerID',
        'amount',
        'type',
        'status',
    ];

    public function lender()
    {
        return $this->belongsTo(Lender::class, 'LenderID');
    }

    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'BorrowerID');
    }
}

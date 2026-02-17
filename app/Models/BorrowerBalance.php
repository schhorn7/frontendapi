<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowerBalance extends Model
{
    //
    protected $table = 'borrowerbalance';
    protected $primaryKey = 'id';

    use HasFactory;
     protected $fillable = [
        'balance',
        'borrowerID'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'borrowerID');
    }
}

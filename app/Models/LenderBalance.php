<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LenderBalance extends Model
{
    //
    protected $table='lenderbalance';
    protected $primaryKey='id';

    
    use HasFactory;
    protected $fillable = [
        'balance',
        'LenderID'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function lender()
    {
        return $this->belongsTo(Lender::class, 'LenderID');
    }
}

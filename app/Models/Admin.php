<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    public function lender()
        {
        return $this->hasMany(Lender::class);
    }
    public function borrower()
        {
        return $this->hasMany(Borrower::class);
    }
    public function request_loan()
        {
        return $this->hasMany(loanRequest::class);
    }
}

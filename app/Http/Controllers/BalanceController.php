<?php

namespace App\Http\Controllers;

use App\Models\BorrowerBalance;
use App\Models\LenderBalance;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    //
    public function getBalanceController($borrowerId) {
        $balance=BorrowerBalance::where('borrowerID', $borrowerId)->first();

        if (!$balance) {
            return response()->json([ 'message'=> 'balance not found'], 404);
        }

        return response()->json([
            'balance'=>$balance,
            'message'=>'fetch balance success'
        ],200);
    }

    // get lender balance
    public function getLenderBalance($lenderId) {
        $balance=LenderBalance::where('LenderID', $lenderId)->first();

        if (!$balance) {
            return response()->json([ 'message'=> 'balance not found'], 404);
        }

        return response()->json([
            'balance'=>$balance,
            'message'=>'fetch balance success'
        ],200);
    }
}

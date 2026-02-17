<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\request_loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class LoanController extends Controller
{
    // show all loan controller
    public function showAllLoan() {
        $loan = Loan::with('borrower')->where('status', 'Active')->get();

        return response()->json([
            'loan'=>$loan,
            'message'=>'success for fetch loan'
        ], 200);
    }
    // show specific loan with id
    public function showLoan($loanid) {
        $loan=Loan::with('borrower')->find($loanid);

        if (!$loan) {
            return response()-> json(['message'=> 'Loan not found'], 404);
        }

        return response()->json([
            'loan'=>$loan,
            'message'=>'success for fetch laon'
        ],200);
    }
}

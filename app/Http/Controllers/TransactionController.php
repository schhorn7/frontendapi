<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\BorrowerBalance;
use App\Models\Lender;
use App\Models\LenderBalance;
use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\loan_after_approve;
use App\Models\transcation;
use Carbon\Carbon;

class TransactionController extends Controller
{
    //
    public function fund($loanId, $lenderId, Request $request) {
        $loan = Loan::find($loanId);
        $borrowerId=$loan->BorrowerID;

        $borrowerData=Borrower::find($borrowerId);
        $request->validate([
            'total'=>'required|numeric|min:1',
        ]);

        // Get validated total amount from request
        $total = $request->input('total');
         // Validate route param manually
        if (!is_numeric($lenderId) || intval($lenderId) != $lenderId) {
            return response()->json(['message' => 'Invalid lender ID'], 400);
        }
       
        $transaction=transcation::create([
            'amount'=> $total,
            'BorrowerID'=>$loan->BorrowerID,
            'LenderID'=>$lenderId,
            'type'=>'fund'


        ]);
        $borrower=BorrowerBalance::where('borrowerID',$borrowerId);
        $lender=LenderBalance::where('LenderId',$lenderId);
        

        $borrower->increment('balance', $total);
        $lender->decrement('balance', $total);

        // $borrower->balance = round($borrower->balance, 2);
        // $borrower->save();

        // $lender->balance = round($lender->balance, 2);
        // $lender->save();

        //update loan status
        $loan->status = 'Funded';
        $loan->save();

        $duration = $loan->request_duration;
        $start    = Carbon::now();
        $amount   = $loan->request_amount;
        $interest = $loan->interest_rate;

        $totalPayment = round($amount * (1+ ($interest/100 * $duration/30)), 2);
        $paymentDate = $start->copy()->addDays($duration);

        loan_after_approve::create([
            'amount'           => $amount,
            'duration'         => $duration,
            'interest_rate'    => $interest,
            'reason'           => $loan->request_reason,
            'employment_status'=> $borrowerData->employment_status,
            'income'           => $borrowerData->income,
            'start_date'       => $start,
            'payment_date'     => $paymentDate,
            'total'            => $totalPayment,
            'BorrowerID'       => $borrowerId,
            'LenderID'         => $lenderId,
            'status'           => 'active',
        ]);

         return response()->json([
            'message' => 'Transaction completed successfully',
            'transaction' => $transaction,
        ]);
            
    }


    public function payback($loanId, $borrowerId, Request $request) {
        $loan = loan_after_approve::find($loanId);
        $lenderId=$loan->LenderID;

        $request->validate([
            'total'=>'required|numeric|min:1',
        ]);

        // Get validated total amount from request
        $total = $request->input('total');
         // Validate route param manually
        if (!is_numeric($borrowerId) || intval($borrowerId) != $borrowerId) {
            return response()->json(['message' => 'Invalid lender ID'], 400);
        }
       
        $transaction=transcation::create([
            'amount'=> $total,
            'BorrowerID'=>$borrowerId,
            'LenderID'=>$loan->LenderID,
            'type'=>'repayment'


        ]);
        $borrower=BorrowerBalance::where('borrowerID',$borrowerId);
        $lender=LenderBalance::where('LenderId',$lenderId);
        

        $borrower->decrement('balance', $total);
        $lender->increment('balance', $total);

        

        //update loan status
        $loan->status = 'completed';
        $loan->save();


         return response()->json([
            'message' => 'Transaction completed successfully',
            'transaction' => $transaction,
        ]);
            
    }

    // get transaction for lender
    public function transactionForLender($lenderId) {
        $transaction = Transcation::with('lender', 'borrower')
            ->where('LenderID', $lenderId)
            ->orderBy('created_at', 'desc')
            ->get();

        if (!$transaction) {
            return response()->json(['message'=>'transaction not found'], 404);
        }

        return response()->json([
            'transaction'=>$transaction,
            'message'=>'fetch transaction successful'
        ], 200);



    }

    // get transaction for borrower
    public function transactionForBorrower($borrowerId) {
        $transaction=transcation::with('lender', 'borrower')->where('BorrowerID', $borrowerId)->orderBy('created_at', 'desc')->get();
        if (!$transaction) {
            return response()->json(['message'=>'transaction not found'], 404);
        }

        return response()->json([
            'transaction'=>$transaction,
            'message'=>'fetch transaction successful'
        ], 200);



    }
}

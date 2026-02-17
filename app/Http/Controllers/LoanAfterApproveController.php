<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Lender;
use App\Models\loan_after_approve;
use BcMath\Number;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanAfterApproveController extends Controller
{
    // get all loan after approve for lender dashboard
    public function showLoanAfterapproveForLender($lenderId) {
        $loan=loan_after_approve::with(['Borrower', 'Lender'])->where('LenderID',$lenderId)->orderBy('id', 'desc')->get();

        if (!$loan) {
            return response()->json(['message'=> 'loan after approve not found'], 404);
        }

        return response()->json([
            'loan'=>$loan,
            'message'=> 'success for fetch loan after approve'
        ],200);
    }
    

    // get all loan after approve for borrower dashboard
    public function showAllLoanAfterapproveForBorrower($borrowerId) {
        $loan=loan_after_approve::with(['Borrower', 'Lender'])->where('BorrowerID',$borrowerId)->orderBy('id', 'desc')->get();

        if (!$loan) {
            return response()->json(['message'=> 'loan after approve not found'], 404);
        }

        return response()->json([
            'loan'=>$loan,
            'message'=> 'success for fetch loan after approve'
        ],200);
    }

    public function showLoanAfterApproveForBorrower ($loanId) {
        $loan=loan_after_approve::with(['Borrower', 'Lender'])->where('id', $loanId)->first();

        if (!$loan) {
            return response()->json(['message'=>'loan not found'], 404);
        }
        return response()->json([
            'loan'=>$loan,
            'message'=> 'success for fetch loan after approve'
        ],200);
        
    }

    // loan tracking 
   
    public function loanTracking($borrowerId)
    {
        $borrower=Borrower::find($borrowerId); 
        $credit_score=$borrower->credit_score;
        
        $loans = loan_after_approve::where('BorrowerID', $borrowerId)->whereIn('status', [ 'late','active'])->get();
        $alert = [];

        foreach ($loans as $loan) {
            $paymentDate = Carbon::parse($loan->payment_date);

            if (!$paymentDate->isFuture()) {
                $loan->status = 'late';
                $loan->save();

                $lender = Lender::find($loan->LenderID);

                $lateDay = floor($paymentDate->diffInDays(now()));
                $lateDayFormat = $lateDay . ' day' . ($lateDay === 1 ? '' : 's');

                $alert[] = [
                    $lender->first_name,
                    $lender->last_name,
                    $lateDayFormat,
                ];

                // ✅ Compute how many 5-day periods have passed
                $penaltyPeriods = floor($lateDay / 5);

                if ($penaltyPeriods > $loan->last_penalized_day) {
                    // Apply new penalty
                    $penaltySteps = $penaltyPeriods - $loan->last_penalized_day;
                    $credit_score = max(0, $credit_score - (5 * $penaltySteps));

                    $borrower->credit_score = $credit_score;
                    $borrower->save();

                    // Update penalty count
                    $loan->last_penalized_day = $penaltyPeriods;
                    $loan->save();
                }
                $loan->lateDay=$lateDay;
                $loan->save();
            }
        }


        return response()->json([
            'alert' => $alert
        ], 200);
    }

}

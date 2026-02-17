<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\request_loan;
use App\Models\Loan;

class RequestLoanController extends Controller
{
    
     /**
     * List all loans.
     */
    // public function index(): JsonResponse
    // {
    //     $loans = Loan::all();
    //     return response()->json($loans, 200);
    // }

    // list all the loan base on id borrower
    public function index(Request $request): JsonResponse 
    {
        $borrowerID=$request->borrowerID;
        $loan = request_loan::where('BorrowerID', $borrowerID)->get();
        return response()->json([
            'loans'=>$loan
        ], 200);

    }

    /**
     * Store a new loan in.
     */
    // public function store(Request $request): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'user_id' => 'required|exists:users,id',
    //         'amount' => 'required|numeric|min:1000',
    //         'interest_rate' => 'required|numeric|min:0|max:100',
    //         'repayment_schedule' => 'required|string',
    //     ]);

    //     $loan = Loan::create($validated);

    //     return response()->json([
    //         'message' => 'Loan created successfully.',
    //         'loan' => $loan
    //     ], 201);
    // }


    //storing the image that borrower upload in the public/storage/uploads in the request_loan table
    public function storeImageUpload(Request $request)
    {
       try {
        // ✅ Make validation more flexible
        $request->validate([
            'identity' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'employment' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $response = [];

        // ✅ Handle identity file if present
        if ($request->hasFile('identity')) {
            $identityFile = $request->file('identity');
            if ($identityFile->isValid()) {
                $identityPath = $identityFile->store('uploads', 'public');
                $response['identity_path'] = "/storage/$identityPath";
            }
        }

        // ✅ Handle employment file if present
        if ($request->hasFile('employment')) {
            $employmentFile = $request->file('employment');
            if ($employmentFile->isValid()) {
                $employmentPath = $employmentFile->store('uploads', 'public');
                $response['employment_path'] = "/storage/$employmentPath";
            }
        }

        // ✅ Return response only if files were processed
        if (empty($response)) {
            return response()->json(['error' => 'No valid files uploaded'], 400);
        }

        return response()->json($response, 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // controller for create the loan request from borrower and store in request_loan table
   public function loanRequestBorrower(Request $request) {
    
        $validated = $request->validate([
            'request_duration' => 'required|integer|min:1|max:180',
            'request_reason' => 'required|string|max:255',
            'request_amount' => 'required|numeric|min:100|max:10000',
            'total' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'request_borrowerID' => 'required|numeric' // Ensure borrower exists
        ]);

        $borrower = Borrower::find($validated['request_borrowerID']);
        
        if (!$borrower) {
            return response()->json([
                'error' => 'Borrower not found'
            ], 404);
        }

        // Determine interest rate based on credit score
        $interestRate = $borrower->credit_score >= 70 ? 5 : 6.5;

        $loan = request_loan::create([
            'request_duration' => $validated['request_duration'],
            'request_reason' => $validated['request_reason'],
            'request_amount' => $validated['request_amount'],
            'interest_rate' => $interestRate,
            'total'=>$validated['total'],
            'BorrowerID' => $validated['request_borrowerID'], // Make sure this matches your DB column
        ]);

        return response()->json([
            
            'loan' => $loan,
            'message' => 'Loan request created successfully'
        ], 200);

    
}



    /**
     * Show specific loan request with loan id use request_loan table.
     */
    public function show(Request $request): JsonResponse
    {
        $loanId=$request->loanID;
        $loan = request_loan::where('request_id', $loanId)->first();

        if (!$loan) {
            return response()->json(['error' => 'Loan not found.'], 404);
        }
        $borrower=Borrower::find($loan->BorrowerID);
       
        return response()->json([
            'loan'=>$loan,
            'borrower'=>$borrower,
        ],200);
    }

    /**
     * Update loan data.
     */
    // public function update(Request $request, Loan $loan): JsonResponse
    // {
    //     $validated = $request->validate([
    //         'status' => 'required|in:pending,active,completed',
    //         'interest_rate' => 'required|numeric|min:0|max:100',
    //         'repayment_schedule' => 'required|string',
    //     ]);

    //     $loan->update($validated);

    //     return response()->json([
    //         'message' => 'Loan updated successfully.',
    //         'loan' => $loan
    //     ], 200);
    // }

    // update the loan data for all field
   
public function update(Request $request, $loanID): JsonResponse 
{
    // ✅ FIXED: Single validation with correct field names
    $validated = $request->validate([
        'request_duration' => 'required|numeric|max:6',
        'request_reason' => 'required|string',
        'request_amount' => 'required|numeric|min:100|max:5000',
        'request_income' => 'required|numeric',
        'request_emp_status' => 'required|string',
        'request_identity_path' => 'required|string',
        'request_employment_path' => 'required|string',
        'request_borrowerID' => 'required|numeric',
        'interest_rate' => 'sometimes|numeric' // Optional field
    ]);
    $loan = request_loan::where('request_id', $loanID)->first();
    if (!$loan) {
        return response()->json(['error' => 'Loan not found.'], 404);
    }

    // ✅ Update loan with validated data
    DB::table('request_loan')->where('request_id', $loanID)->update([
        'request_duration' => $validated['request_duration'],
        'request_reason' => $validated['request_reason'],
        'request_amount' => $validated['request_amount'],
        'interest_rate' => $validated['interest_rate'] ?? 5, // Use provided or default to 5
        'income' => $validated['request_income'],
        'employment_status' => $validated['request_emp_status'],
        'identity_path' => $validated['request_identity_path'],
        'employment_path' => $validated['request_employment_path'],
        'BorrowerID' => $validated['request_borrowerID'],
    ]);

    // Refresh the loan instance
    $loan = request_loan::where('request_id', $loanID)->first();

    return response()->json([
        'msg' => 'Loan updated successfully',
        'loan' => $loan
   
    ], 200);
}




    /**
     * Delete a loan.
     */
    public function destroy(Loan $loan): JsonResponse
    {
        $loan->delete();

        return response()->json(['message' => 'Loan deleted successfully.'], 200);
    }
    /**
     * Filter loans by status.
     */

    public function filter($status): JsonResponse
    {
        $loans = Loan::where('status', $status)->get();

        return response()->json($loans, 200);
    }
}

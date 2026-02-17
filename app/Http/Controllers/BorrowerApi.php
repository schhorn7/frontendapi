<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
//use App\Models\borrower as ModelsBorrower;
use App\Models\Loan;
use App\Models\BorrowerBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\BorrowerMail;

class BorrowerApi extends Controller

{
    public function changePhoneNumber(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
            'phone_number' => 'required'
        ]);
        $user = Borrower::find($request->id);
        $user->phone_number = $request->phone_number;
        $user->save();
        return response()->json(['message' => 'Phone Number updated successfully']);
    }
    public function changePassword(Request $request) {
        //need user to input old password then compare if it is right
        $validated = $request->validate([
            'id' => 'required',
            'current_password' => 'required',
            'new_password' => 'required'
        ]);
        $user = Borrower::find($request->id);
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
            return response()->json(['message' => 'Password updated successfully']);

    }
    public function changeEmail(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
            'email' => 'required'
        ]);
        $user = Borrower::find($request->id);
        $user->email = $request->email;
        $user->save();
        return response()->json(['message' => 'Email updated successfully']);

    }
    public function changeName(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
            'current_last_name' => 'required',
            'current_first_name' => 'required'
        ]);
        $user = Borrower::find($request->id);
        $user->last_name = $request->last_name;
        $user->last_name = $request->first_name;
        $user->save();
        return response()->json(['message' => 'Name updated successfully']);
    }
    public function listLoan() {
        $value = Loan::all();
        return response()->json([
            "Loan" => $value
        ]);

    }


    //storing the image that borrower upload in the public/storage/uploads
   public function storeImageUpload(Request $request)
    {
       try {
        // ✅ Make validation more flexible
        $request->validate([
            'profileUpload' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'employment' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $response = [];

        // ✅ Handle identity file if present
        if ($request->hasFile('profileUpload')) {
            $identityFile = $request->file('profileUpload');
            if ($identityFile->isValid()) {
                $identityPath = $identityFile->store('uploads', 'public');
                $response['profile_path'] = "/storage/$identityPath";
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





    public function loanRequestBorrower(Request $request) {
        //$id = 1;
        //Borrower::where('id', $id)->increment('balance', 10);
        $validated = $request->validate([
            'request_duration' => 'required',
            'request_reason'=> 'required',
            'request_amount' => 'required',

            'request_income'=> 'required',
            'request_emp_status'=>'required',
            'request_identity_path'=>'required',
            'request_employment_path'=>'required',
        ]);


        $borrowerID = Borrower::where('id', Auth::guard('borrower')->id())->first();
        if ($borrowerID->credit_score > 70) {
            $loan = Loan::create([
                'request_duration' => $validated['request_duration'],
                'request_reason'=> $validated['request_reason'],
                'request_amount' => $validated['request_amount'],
                'interest_rate' => 5,
                'income' => $validated['request_income'],
                'employment_status' => $validated['request_emp_status'],
                'identity_path' => $validated['request_identity_path'],
                'employment_path' => $validated['request_employment_path'],
                'BorrowerID' => Auth::guard('borrower')->id(),
            ]);
        } else {
            $loan = Loan::create([
                'request_duration' => $validated['request_duration'],
                'request_reason'=> $validated['request_reason'],
                'request_amount' => $validated['request_amount'],
                'interest_rate' => 6.5,
                'income' => $validated['request_income'],
                'employment_status' => $validated['request_emp_status'],
                'identity_path' => $validated['request_identity_path'],
                'employment_path' => $validated['request_employment_path'],
                'BorrowerID' => Auth::guard('borrower')->id(),

            ]);

        }

        return response()->json([
            "Loan" => $loan
        ]);
    }
    public function registerBorrower(Request $request) {
        $randomNumber = mt_rand(100000, 999999);
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone_number' => 'required',
            'income'=>'required',
            'identity_path' => 'required|string',
            'employment_path' => 'required|string'
        ]);

        $user = Borrower::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'otp' => $randomNumber,
            'credit_score' => 100,
            'income'=>$validated['income'],
            'status' => 'Inactive',
            'identity_path' => $validated['identity_path'],
            'employment_path' => $validated['employment_path'],
                ]);
                BorrowerBalance::create([
                    'borrowerID'=> $user->id,
                    'balance' => 0,

                ]);
                $borrower = Borrower::where('email', $request->email)->first();
                $otp = $borrower->otp;
                Mail::to($borrower->email)->send(new BorrowerMail($otp));
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'borrower' => $user,
                'token' => $token,
            ]);
            }

    public function getApprove() {
       $borrower = Borrower::orderBy('created_at', 'desc')->first();
       if ($borrower->approval_status==='Approved') {
        
        return response()->json([
            'borrower'=>$borrower,
            
        ]);
       }

       return response()->json([
        'msg'=>'you infomation are in verify step',
       ]);
    }

    public function loginBorrower(Request $request) {
        //query database : user table : field email
        $user = Borrower::where('email', $request->email)->first();

        //check if the password user entered is the same as the one in db
        //and also check if user exist or not
        if ($user->approval_status==='Approved') {
            if (! $user || ! Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            } else {
                return ['token' => $user->createToken('api-token')->plainTextToken];
            }
        } else if ($user->approval_status==='Rejected') {
            return response()->json([
            'msg'=>'your Account was rejected'
        ]);
        }
        return response()->json([
            'msg'=>'your acc not approve yet'
        ]);
    }
    public function listAllBorrower() {
        $value = Borrower::all();
        return response()->json([
            'job' => $value,
        ],
            200
        );
    }
    public function logoutBorrower(Request $request) {
        $request->user('borrower')->tokens()->delete();
        return [
            "message" => "you are logged out"
        ];
    }
    public function verifyOTPBorrower(Request $request)
    {
        $user = $request->user('borrower');
        //if ($request->user()->tokens()) {
        if (!$user || !$user->currentAccessToken()) {
            return response()->json([
                'message' => 'wrong token'
            ]);
        }
        $request->validate([
            'otp' => 'required'
        ]);

        if ($user->otp === $request->otp) {
            $user->otp_verified = true;
            $user->save();
            return response()->json([

                'approve' => 'otp verified'

            ]);
        } else {
            return response()->json([
                'reject' => 'invalid otp'
            ]);
        }
    }


    /*
    public function register(Request $request) {
            $randomNumber = mt_rand(100000, 999999);
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:6',
                'phone_number' => 'required'
            ]);
            //$create = DB::select('insert into borrowers (first_name, last_name, email, otp, password, phone_number, amount, balance, credit_score) values($request->first_name,$request last_name, $request->email, $randomNumber, Hash::make($request->password), $request->phone_number, 0, 0, 100);');
            $borrower = Borrower::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'otp' => $randomNumber,
                'password' => Hash::make($request->password),
                'phone_number' => $request->phone_number,
                'amount' => 0,
                'balance' => 0,
                'credit_score' => 100,
                'status' => 'Pending'

        ]);
            // 🔒 Log the borrower in using the correct guard
            Auth::guard('borrower')->login($borrower);

    }
    */

    public function editProfile(Request $request)
{
    $validated = $request->validate([
        'firstname'          => 'nullable|string',
        'lastname'           => 'nullable|string',
        'email'              => 'nullable|email',
        'phone_number'       => 'nullable|string',
        'income'             => 'nullable|numeric',
        'employment_status'  => 'nullable|string',
        'profile_path'       => 'nullable|string',
        'province'           => 'nullable|string',
        'employment_path'    => 'nullable|string', // path, not a file
        'borrowerId'=>'required',
    ]);

    $borrower = Borrower::find($validated['borrowerId']);

    if (!$borrower) {
        return response()->json(['message' => 'Borrower not found'], 404);
    }

    // Update fields
    $borrower->first_name = $validated['firstname'];
    $borrower->last_name = $validated['lastname'];
    $borrower->email = $validated['email'];
    $borrower->phone_number = $validated['phone_number'];
    $borrower->income = $validated['income'];
    $borrower->employment_status = $validated['employment_status'];
    $borrower->province = $validated['province'];
    $borrower->employment_path = $validated['employment_path'] ?? $borrower->employment_path;
    $borrower->profile_picture = $validated['profile_path'];

    $borrower->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'data' => $borrower
    ]);
}
}

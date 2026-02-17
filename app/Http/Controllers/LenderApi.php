<?php

namespace App\Http\Controllers;

use App\Models\Lender;
use App\Models\LenderBalance;
use App\Models\Loan;
use App\Models\loan_after_approve;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\lenderMail;

class LenderApi extends Controller
{
    public function changePhoneNumber(Request $request) {
        $validated = $request->validate([
            'id' => 'required',
            'phone_number' => 'required'
        ]);
        $user = Lender::find($request->id);
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
        $user = Lender::find($request->id);
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
        $user = Lender::find($request->id);
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
        $user = Lender::find($request->id);
        $user->last_name = $request->last_name;
        $user->last_name = $request->first_name;
        $user->save();
        return response()->json(['message' => 'Name updated successfully']);
    }

    public function listLoanAccept() {
        return response()->json([

            "loan" => loan_after_approve::all()
        ]);
    }
    public function lenderAcceptLoan(Request $request) {
        $loan = Loan::find($request->id);
        $loanCopy = loan_after_approve::create([
            'amount' => $loan->request_amount,
            'duration' => $loan->request_duration,
            'reason' => $loan->request_reason,
            'interest_rate' => $loan->interest_rate,
            'BorrowerID' => $loan->BorrowerID,
            'LenderID' => Auth::guard('lender')->id(),
            'payment_date' => Carbon::now()->addDays($loan->request_duration),
        ]);

        return response()->json([
            "Lender ID" => $loanCopy->LenderID,
            "Borrower ID" => $loanCopy->BorrowerID,
            "Amount" => $loanCopy->amount,
            "Interest Rate" => $loanCopy->interest_rate,
            "Payment Date" => $loanCopy->payment_date
        ]);

    }
    public function registerLender(Request $request) {
        $randomNumber = mt_rand(100000, 999999);
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'phone_number' => 'required'
        ]);
        $user = Lender::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'otp' => $randomNumber,
            'amount' => 0,
            'status' => 'Inactive'

        ]);

        LenderBalance::create([
            'balance'=>10000,
            'LenderID'=>$user->id,
        ]);
        $lender = Lender::where('email', $request->email)->first();
        $otp = $user->otp;
        Mail::to($lender->email)->send(new lenderMail($otp));
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'lender' => $user,
        'token' => $token,
    ]);
    }

    public function loginLender(Request $request) {
        //query database : user table : field email
        $user = Lender::where('email', $request->email)->first();

        //check if the password user entered is the same as the one in db
        //and also check if user exist or not
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        return ['token' => $user->createToken('api-token')->plainTextToken];
    }
    public function listAllLender() {
        $value = Lender::all();
        return response()->json([
            'job' => $value,
        ],
            200
        );
    }
    public function logoutLender(Request $request) {
        $request->user('lender')->tokens()->delete();
        return [
            "message" => "you are logged out"
        ];
    }
    public function verifyOTPLender(Request $request) {
        $user = $request->user('lender');
        //if ($request->user()->tokens()) {
        if(!$user || !$user->currentAccessToken()) {
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


        //return response()->json(['message' => 'otp verified']);
    }

    public function storeImageUploadForLender(Request $request) {
         try {
        // ✅ Make validation more flexible
        $request->validate([
            'profileUpload' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            
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

    public function editProfileLender(Request $request) {
        $validated = $request->validate([
        'firstname'          => 'nullable|string',
        'lastname'           => 'nullable|string',
        'email'              => 'nullable|email',
        'phone_number'       => 'nullable|string',
        
        'profile_path'       => 'nullable|string',
        'province'           => 'nullable|string',
       
        'lenderId'=>'required',
    ]);

    $lender = Lender::find($validated['lenderId']);

    if (!$lender) {
        return response()->json(['message' => 'Borrower not found'], 404);
    }

    // Update fields
    $lender->first_name = $validated['firstname'];
    $lender->last_name = $validated['lastname'];
    $lender->email = $validated['email'];
    $lender->phone_number = $validated['phone_number'];
    
    $lender->province = $validated['province'];
    
    $lender->profile_picture = $validated['profile_path'];

    $lender->save();

    return response()->json([
        'message' => 'Profile updated successfully',
        'data' => $lender
    ]);
    }
}

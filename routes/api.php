<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BorrowerApi;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LenderApi;
use App\Http\Controllers\LoanAfterApproveController;
use App\Http\Controllers\RequestLoanController;
use App\Http\Controllers\TranctionController;
use App\Http\Controllers\TransactionController;
use App\Models\LenderBalance;
use Database\Seeders\LoanSeeder;

Route::post('/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'user' => $user,
        'token' => $token,
    ]);
});

// Protected routes (must be authenticated with Sanctum token)
Route::middleware('auth:sanctum')->prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index']);
    Route::patch('/users/{user}/verify', [AdminUserController::class, 'verify']);
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve']);
    Route::patch('/users/{user}/suspend', [AdminUserController::class, 'suspend']);

    Route::apiResource('loans', LoanController::class);
});



//user api
//Route::get('listAll', [LoginController::class, 'listAll']);
Route::post('register', [LoginController::class, 'register']);
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
Route::post('verifyOTP', [LoginController::class, 'verifyOTP'])->middleware('auth:sanctum');

//borrower api

Route::get('listAllBorrower', [BorrowerApi::class, 'listAllBorrower']);
Route::post('/borrower/register', [BorrowerApi::class, 'registerBorrower']);
Route::post('/borrower/login', [BorrowerApi::class, 'loginBorrower']);
Route::get('ApproveForBorrower', [BorrowerApi::class, 'getApprove']);
Route::post('/borrower/logout', [BorrowerApi::class, 'logoutBorrower'])->middleware('auth:borrower');
Route::post('/borrower/verifyOTP', [BorrowerApi::class, 'verifyOTPBorrower'])->middleware('auth:borrower');
Route::middleware(['auth:sanctum', 'verified_user'])->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Welcome to the dashboard']);
    });
});
Route::middleware('auth:sanctum')->get('/borrower', function (Request $request) {
    return response()->json($request->user('borrower'));
});
Route::post('/storeImageForEdit', [BorrowerApi::class, 'storeImageUpload']);
Route::post('/editProfileforBorrower', [BorrowerApi::class, 'editProfile']);


// get balance for borrower
Route::get('getBorrowerBalance/{borrowerId}', [BalanceController::class, 'getBalanceController']);

//lender api
Route::get('listAllLender', [LenderApi::class, 'listAllLender']);
Route::post('/lender/register', [LenderApi::class, 'registerLender']);
Route::post('/lender/login', [LenderApi::class, 'loginLender']);
Route::post('/lender/logout', [LenderApi::class, 'logoutLender'])->middleware('auth:lender');
Route::post('/lender/verifyOTP', [LenderApi::class, 'verifyOTPLender'])->middleware('auth:lender');
Route::middleware('auth:sanctum')->get('/lender', function (Request $request) {
    return response()->json($request->user('lender'));
});
Route::post('storeImageForEditLender', [LenderApi::class, 'storeImageUploadForLender']);
Route::post('editProfileforLender', [LenderApi::class, 'editProfileLender']);

// get balance for lender
Route::get('getLenderBalance/{lenderId}', [BalanceController::class, 'getLenderBalance']);

//transaction api
//Route::post('add', [BorrowerApi::class, 'loanRequestBorrower']);


// request loan
Route::post('/storeImage', [RequestLoanController::class, 'storeImageUpload']);
Route::post('/loanRequest', [RequestLoanController::class, 'loanRequestBorrower']);
Route::get('/allLoan/{borrowerID}', [RequestLoanController::class, 'index']);
Route::get('/loan/{loanID}', [RequestLoanController::class , 'show']);
Route::post('loanUpdate/{loanID}', [RequestLoanController::class, 'update']);

//loan request
//Route::post('/loanRequest', [BorrowerApi::class, 'loanRequestBorrower'])->middleware('auth:borrower');
//list loan
//Route::get('listLoan', [BorrowerApi::class, 'listLoan']);
//accept loan
Route::post('acceptLoan', [LenderApi::class, 'lenderAcceptLoan'])->middleware('auth:lender');
//list loan after accept
//localhost:8000/api/listLoanAccept
Route::get('listLoanAccept', [LenderApi::class, 'listLoanAccept']);

// using loan table
Route::get('getAllLoan', [LoanController::class, 'showAllLoan']);
Route::get('getLoan/{loanid}', [LoanController::class, 'showLoan']);


//using loan after approve table for lender in fundRecord
Route::get('getAllLoanAfterApproveforLender/{lenderId}', [LoanAfterApproveController::class, 'showLoanAfterapproveForLender']);



//using loand after approve table for borrower in creditRecord
Route::get('getAllLoanAfterApproveforBorrower/{borrowerId}', [LoanAfterApproveController::class, 'showAllLoanAfterapproveForBorrower']);

// get the the loan after approve with id
Route::get('getLoanAfterApproveforBorrower/{loanId}',[LoanAfterApproveController::class, 'showLoanAfterApproveForBorrower']);

// transaction table
Route::post('fund/{loanId}/{lenderId}', [TransactionController::class, 'fund']);

// using loan after approve table
Route::post('payback/{loanId}/{borrowerId}', [TransactionController::class, 'payback']);

// get the history transaction for lender
Route::get('transactionForLender/{lenderId}', [TransactionController::class, 'transactionForLender']);

Route::get('transactionForBorrower/{borrowerId}', [TransactionController::class, 'transactionForBorrower']);

Route::get('loanTracking/{borrowerId}', [LoanAfterApproveController::class, 'loanTracking']);

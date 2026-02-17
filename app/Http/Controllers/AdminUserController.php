<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::whereIn('role', ['Borrower', 'Lender'])->get();
        return response()->json($users, 200);
    }

    public function approve(User $user): JsonResponse
    {
        $user->update(['status' => 'Approved']);
        return response()->json([
            'message' => 'User approved.',
            'user' => $user
        ], 200);
    }

    public function suspend(User $user): JsonResponse
    {
        $user->update(['status' => 'Suspended']);
        return response()->json([
            'message' => 'User suspended.',
            'user' => $user
        ], 200);
    }
}

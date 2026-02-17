<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Lender;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index() : JsonResponse
    {
        $totalLenders = Lender::count();
        $totalBorrowers = Borrower::count();
        $activeLenders = Lender::where('status', 'active')->count();
        $activeBorrowers = Borrower::where('status', 'active')->count();
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalLenders + $totalBorrowers,
                'active_users' => $activeLenders + $activeBorrowers,
                'total_lenders' => $totalLenders,
                'total_borrowers' => $totalBorrowers,
                'active_lenders' => $activeLenders,
                'active_borrowers' => $activeBorrowers,
            ]
        ]);
    }

    public function showallUser(Request $request) : JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('limit', 10);
        $offset = ($page - 1) * $perPage;

        // Get all users from both tables
        $lenders = Lender::all()->map(function($lender) {
            return [
                'id' => $lender->id,
                'table_type' => 'lenders',
                'first_name' => $lender->first_name,
                'last_name' => $lender->last_name,
                'email' => $lender->email,
                'phone_number' => $lender->phone_number,
                'status' => $lender->status,
            ];
        });

        $borrowers = Borrower::all()->map(function($borrower) {
            return [
                'id' => $borrower->id,
                'table_type' => 'borrowers',
                'first_name' => $borrower->first_name,
                'last_name' => $borrower->last_name,
                'email' => $borrower->email,
                'phone_number' => $borrower->phone_number,
                'status' => $borrower->status,
            ];
        });

        $allUsers = $lenders->concat($borrowers)->sortBy('first_name')->values();

        // Calculate pagination info
        $totalUsers = $allUsers->count();
        $totalPages = ceil($totalUsers / $perPage);

        $paginatedUsers = $allUsers->slice($offset, $perPage)->values();

        $activeLenders = Lender::where('status', 'active')->count();
        $activeBorrowers = Borrower::where('status', 'active')->count();

        return response()->json([
            'success' => true,
            'data' => $paginatedUsers,
            'pagination' => [
                'current_page' => (int)$page,
                'per_page' => (int)$perPage,
                'total' => $totalUsers,
                'total_pages' => $totalPages,
                'has_next_page' => $page < $totalPages,
                'has_previous_page' => $page > 1
            ],
            'stats' => [
                'active_users' => $activeLenders + $activeBorrowers,
                'total_lenders' => $lenders->count(),
                'total_borrowers' => $borrowers->count(),
                'active_lenders' => $activeLenders,
                'active_borrowers' => $activeBorrowers
            ]
        ]);
    }

    public function updateUser(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'table_type' => 'required|in:lenders,borrowers',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'required|string|max:20',
                'status' => 'required|in:active,pending,suspended,inactive', // Changed to lowercase to match frontend
            ]);

            $user = $request->table_type === 'lenders'
                ? Lender::findOrFail($id)
                : Borrower::findOrFail($id);

            $user->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User update failed. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteUser(Request $request, $id): JsonResponse
    {
        try {
            $request->validate([
                'table_type' => 'required|in:lenders,borrowers',
            ]);

            $user = $request->table_type === 'lenders'
                ? Lender::findOrFail($id)
                : Borrower::findOrFail($id);

            $userName = $user->first_name . ' ' . $user->last_name;
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => "User {$userName} deleted successfully"
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            Log::error('Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'User deletion failed. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

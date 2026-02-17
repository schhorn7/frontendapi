<?php

// app/Http/Middleware/EnsureOtpIsVerified.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->is_verified) {
            return response()->json(['message' => 'Account not verified by admin'], 403);
        }

        return $next($request);
    }
}

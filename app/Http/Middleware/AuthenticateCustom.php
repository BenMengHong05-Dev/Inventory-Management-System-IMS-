<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCustom
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = session('user_id');

        if (!$userId) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.',
                    'data' => null,
                    'errors' => ['auth' => ['You must be logged in.']]
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        // Verify user exists and is ACTIVE via raw SQL
        $users = DB::select("SELECT id, name, email, status FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1", [$userId]);
        if (empty($users) || $users[0]->status !== 'ACTIVE') {
            session()->flush();
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Account is deactivated or does not exist.',
                    'data' => null,
                    'errors' => ['auth' => ['Account is deactivated.']]
                ], 403);
            }
            return redirect()->route('login')->with('error', 'Your account is inactive. Please contact administrator.');
        }

        return $next($request);
    }
}

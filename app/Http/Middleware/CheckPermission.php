<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!has_permission($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action. You do not have permission: ' . $permission,
                    'data' => null,
                    'errors' => ['permission' => ['Access denied.']]
                ], 403);
            }

            abort(403, 'Unauthorized. You do not have permission to perform this action (' . $permission . ').');
        }

        return $next($request);
    }
}

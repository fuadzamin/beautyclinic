<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Check that the authenticated staff has one of the allowed roles.
     * Usage in routes: middleware('role:owner') or middleware('role:owner,admin_klinik')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $staff = Auth::guard('api_staff')->user();

        if (!$staff) {
            return response()->json([
                'success'   => false,
                'message'   => 'Unauthenticated',
                'timestamp' => now()->toISOString(),
            ], 401);
        }

        if (!in_array($staff->role, $roles)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Forbidden: insufficient permissions',
                'timestamp' => now()->toISOString(),
            ], 403);
        }

        return $next($request);
    }
}

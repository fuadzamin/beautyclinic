<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Customer register
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'address'  => $request->address,
        ]);

        $token = Auth::guard('api')->login($user);

        return $this->respondWithToken($token, $user, 201);
    }

    // Unified login
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        // Try Customer first
        if ($token = Auth::guard('api')->attempt($credentials)) {
            $user = Auth::guard('api')->user();
            $user->update(['last_login' => Carbon::now()]);
            return $this->respondWithToken($token, $user);
        }

        // Try Staff
        if ($token = Auth::guard('api_staff')->attempt($credentials)) {
            $staff = Auth::guard('api_staff')->user();

            if (!$staff->is_active) {
                Auth::guard('api_staff')->logout();
                return $this->error('Account is inactive. Contact your administrator.', 403);
            }

            // Check 2FA
            if ($staff->two_fa_enabled) {
                // Logout immediately, don't give the real token yet
                Auth::guard('api_staff')->logout();
                
                return response()->json([
                    'success'      => true,
                    'requires_2fa' => true,
                    'staff_id'     => $staff->id,
                    'message'      => 'Two-factor authentication required',
                ]);
            }

            $staff->update(['last_login' => Carbon::now()]);
            return $this->respondWithToken($token, $staff);
        }

        return $this->error('Invalid credentials', 401);
    }

    // Verify 2FA code
    public function verify2fa(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|exists:staff,id',
            'code'     => 'required|string|size:6',
        ]);

        $staff = Staff::findOrFail($request->staff_id);
        
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $valid = $google2fa->verifyKey($staff->two_fa_secret, $request->code);

        if (!$valid) {
            return $this->error('Invalid 2FA code', 401);
        }

        $token = Auth::guard('api_staff')->login($staff);
        $staff->update(['last_login' => Carbon::now()]);

        return $this->respondWithToken($token, $staff);
    }

    // Get authenticated user
    public function me(): JsonResponse
    {
        $user = Auth::guard('api')->user() ?? Auth::guard('api_staff')->user();

        return response()->json([
            'success'   => true,
            'data'      => $user,
            'message'   => 'Profile retrieved',
            'timestamp' => now()->toISOString(),
        ]);
    }

    // Logout
    public function logout(): JsonResponse
    {
        $guard = Auth::guard('api')->check() ? 'api' : 'api_staff';
        Auth::guard($guard)->logout();

        return response()->json([
            'success'   => true,
            'message'   => 'Logged out successfully',
            'timestamp' => now()->toISOString(),
        ]);
    }

    // Refresh token
    public function refresh(): JsonResponse
    {
        $guard = Auth::guard('api')->check() ? 'api' : 'api_staff';
        $token = Auth::guard($guard)->refresh();

        return $this->respondWithToken($token, Auth::guard($guard)->user());
    }

    private function respondWithToken(string $token, $user, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'user'         => $user,
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => config('jwt.ttl') * 60,
            ],
            'message'   => 'Success',
            'timestamp' => now()->toISOString(),
        ], $status);
    }
}

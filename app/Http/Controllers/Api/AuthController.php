<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Check session authentication status (detailed)
     */
    public function sessionCheck(Request $request): JsonResponse
    {
        try {
            $isAuthenticated = Auth::check();
            $sessionId = session()->getId();
            $user = null;
            $sessionData = [];

            if ($isAuthenticated) {
                $user = Auth::user();
                $sessionData = [
                    'session_started' => session()->get('login_web_' . sha1('web')),
                    'last_activity' => session()->get('last_activity', now()),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => $isAuthenticated ? 'Session aktif' : 'Session tidak aktif',
                'data' => [
                    'authenticated' => $isAuthenticated,
                    'session_id' => $sessionId,
                    'session_active' => session()->isStarted(),
                    'user' => $isAuthenticated ? [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'last_login' => $sessionData['last_activity'] ?? null,
                    ] : null,
                    'session_info' => [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'started_at' => $sessionData['session_started'] ?? null,
                    ],
                    'expires_in' => config('session.lifetime') * 60, // in seconds
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate session and refresh if needed
     */
    public function validateSession(Request $request): JsonResponse
    {
        try {
            $isAuthenticated = Auth::check();

            if (!$isAuthenticated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session expired atau tidak valid',
                    'data' => [
                        'authenticated' => false,
                        'session_expired' => true
                    ]
                ], 401);
            }

            // Update last activity
            session()->put('last_activity', now());

            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Session valid',
                'data' => [
                    'authenticated' => true,
                    'session_id' => session()->getId(),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                    'last_activity' => now()->toISOString(),
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get API token for authenticated user
     */
    public function getToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'token_name' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password salah',
                ], 401);
            }

            // Generate token name
            $tokenName = $request->token_name ?? 'API Token - ' . now()->format('Y-m-d H:i:s');

            // Create token
            $token = $user->createToken($tokenName);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dibuat',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                    ],
                    'token' => $token->plainTextToken,
                    'token_name' => $tokenName,
                    'token_type' => 'Bearer',
                    'expires_at' => null,
                    'created_at' => now()->toISOString(),
                    'usage_instructions' => [
                        'header_name' => 'Authorization',
                        'header_value' => 'Bearer ' . $token->plainTextToken,
                        'example' => 'Authorization: Bearer ' . $token->plainTextToken
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Logout user (handles both session and token)
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            // Check if user is authenticated via token (Sanctum)
            if ($request->user()) {
                // Token-based logout
                $request->user()->currentAccessToken()->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout berhasil (token dihapus)'
                ], 200);
            }

            // Check if session is available for session-based logout
            if ($request->hasSession()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => true,
                    'message' => 'Logout berhasil (session dihapus)'
                ], 200);
            }

            // If neither token nor session is available
            return response()->json([
                'success' => false,
                'message' => 'User sudah logout atau tidak terautentikasi'
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Session-only logout
     */
    public function sessionLogout(Request $request): JsonResponse
    {
        try {
            if (!$request->hasSession()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session tidak tersedia'
                ], 400);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Session logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout session',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Token-based logout
     */
    public function tokenLogout(Request $request): JsonResponse
    {
        try {
            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil dihapus, logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revoke all tokens for user
     */
    public function revokeAllTokens(Request $request): JsonResponse
    {
        try {
            // Revoke all tokens for the authenticated user
            $deletedCount = $request->user()->tokens()->count();
            $request->user()->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua token berhasil dihapus',
                'data' => [
                    'revoked_tokens_count' => $deletedCount
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus token',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

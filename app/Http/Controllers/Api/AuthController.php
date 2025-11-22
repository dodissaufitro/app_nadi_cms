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
     * Login user and create session
     */
    public function login(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                if ($request->hasSession()) {
                    $request->session()->regenerate();
                }

                $user = Auth::user();

                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'data' => [
                        'user' => [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'email_verified_at' => $user->email_verified_at,
                            'created_at' => $user->created_at,
                        ],
                        'session_id' => $request->hasSession() ? $request->session()->getId() : null,
                    ]
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Token-based login (Sanctum)
     */
    public function tokenLogin(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string|min:6',
                'device_name' => 'string|max:255'
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

            $deviceName = $request->device_name ?? $request->userAgent();
            $token = $user->createToken($deviceName);

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                    ],
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => null,
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
     * Check authentication status
     */
    public function check(Request $request): JsonResponse
    {
        try {
            $isAuthenticated = Auth::check();

            return response()->json([
                'success' => true,
                'message' => $isAuthenticated ? 'User terautentikasi' : 'User tidak terautentikasi',
                'data' => [
                    'authenticated' => $isAuthenticated,
                    'user_id' => $isAuthenticated ? Auth::id() : null
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
     * Get authenticated user data
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                        'updated_at' => $user->updated_at,
                    ],
                    'authenticated' => true
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
     * Change password
     */
    public function changePassword(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama tidak cocok'
                ], 422);
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah password',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refresh token (create new token)
     */
    public function refreshToken(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $deviceName = $request->device_name ?? $request->userAgent();

            // Revoke current token
            $request->user()->currentAccessToken()->delete();

            // Create new token
            $token = $user->createToken($deviceName);

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil diperbarui',
                'data' => [
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat refresh token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user tokens info
     */
    public function tokens(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $tokens = $user->tokens()->select(['id', 'name', 'last_used_at', 'created_at'])->get();

            return response()->json([
                'success' => true,
                'message' => 'Data token berhasil diambil',
                'data' => [
                    'current_token_id' => $request->user()->currentAccessToken()->id,
                    'tokens' => $tokens
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

    /**
     * Register new user
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Generate token for the new user
            $tokenName = $request->device_name ?? 'Registration Device';
            $token = $user->createToken($tokenName);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'email_verified_at' => $user->email_verified_at,
                        'created_at' => $user->created_at,
                    ],
                    'token' => $token->plainTextToken,
                    'token_type' => 'Bearer',
                    'expires_at' => null,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register new user without auto token generation
     */
    public function registerWithoutToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ], [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil, silakan login',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'created_at' => $user->created_at,
                    ]
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset/Revoke specific token by ID
     */
    public function resetToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'token_id' => 'required|integer|exists:personal_access_tokens,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();
            $token = $user->tokens()->where('id', $request->token_id)->first();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan atau bukan milik anda'
                ], 404);
            }

            $tokenName = $token->name;
            $token->delete();

            return response()->json([
                'success' => true,
                'message' => 'Token berhasil direset',
                'data' => [
                    'token_name' => $tokenName,
                    'deleted_at' => now()->toISOString()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset all tokens except current
     */
    public function resetAllTokensExceptCurrent(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $currentTokenId = $request->user()->currentAccessToken()->id;

            $deletedCount = $user->tokens()
                ->where('id', '!=', $currentTokenId)
                ->count();

            $user->tokens()
                ->where('id', '!=', $currentTokenId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua token lain berhasil direset',
                'data' => [
                    'revoked_tokens_count' => $deletedCount,
                    'current_token_id' => $currentTokenId
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset token',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset all tokens and create new one
     */
    public function resetAndCreateToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_name' => 'string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->user();

            // Count tokens before delete
            $deletedCount = $user->tokens()->count();

            // Delete all existing tokens
            $user->tokens()->delete();

            // Create new token
            $deviceName = $request->device_name ?? 'New Device - ' . now()->format('Y-m-d H:i:s');
            $token = $user->createToken($deviceName);

            return response()->json([
                'success' => true,
                'message' => 'Semua token lama berhasil direset dan token baru dibuat',
                'data' => [
                    'revoked_tokens_count' => $deletedCount,
                    'new_token' => $token->plainTextToken,
                    'token_name' => $deviceName,
                    'token_type' => 'Bearer',
                    'created_at' => now()->toISOString()
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset dan membuat token baru',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

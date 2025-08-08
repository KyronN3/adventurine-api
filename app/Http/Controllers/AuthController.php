<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    /*
     * Register Users ❤️❤️❤️
    */

    public function register(RegisterUserRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
        ]);

        // spatie-permission assigning Roles |||  Check spatie Docs. ❤️❤️❤️
        $user->assignRole($request->validated()['role']);

        $token = $user->createToken(
            Str::lower($request->validated()['name']) . '_auth_token', ['*'],
            now()->addDays(7))
            ->plainTextToken;

        /*
         * Storing token in Cookie
        */
        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'created_at' => $user->created_at,
            'token' => $token,
        ], 201)->withCookie(
            cookie(
                'api_token',
                $token,
                7 * 24 * 60, // 7 days expiration same as the Token Above line 30 ❤️❤️❤️
                '/',
                null,
                true,
                true,
                false,
                'Lax'));

    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        // delete all tokens in the authenticated user | only single login account per device ❤️❤️❤️
        $user->tokens()->delete();

        $token = $user->createToken(
            Str::lower($credentials['email']) . '_auth_token', ['*'],
            now()->addDays(7)
        )->plainTextToken;

        /*
         * Storing token in Cookie ❤️❤️❤️
        */

        return response()->json([
            'success' => true,
            'user' => $user->only('id', 'name', 'email', 'role'),
        ])->withCookie(cookie(
            'api_token',
            $token,
            7 * 24 * 60, // 7 days expiration same as the Token Above line 30 ❤️❤️❤️
            '/',
            null,
            true,
            true,
            false,
            'Strict'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ])->withCookie(
            cookie()->forget('api_token')
        );
    }
}

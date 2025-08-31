<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Components\ResponseFormat;
use App\Events\DashboardEvent;
use App\Exceptions\assignRoleException;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /*
     * Register Users ❤️❤️❤️
    */

    public function register(RegisterUserRequest $request): JsonResponse
    {

        try {
            $user = DB::transaction(function () use ($request) {
                // If anything fail in here just automatically rollback in short data will be not created ❤️❤️❤️
                $user = User::create([
                    'name' => $request->validated()['name'],
                    'email' => $request->validated()['email'],
                    'position' => $request->validated()['position'],
                    'office' => $request->validated()['office'],
                    'password' => Hash::make($request->validated()['password']),
                ]);
                $user->assignRole($request->validated()['role']);

                return $user;
            });

            return ResponseFormat::creationSuccess('Created Successfully', $request->validated()['role'], $user->created_at, $user->only(['name', 'email']), 201);
        } catch (\Exception $e) {
            return ResponseFormat::error($e->getMessage(), $e->getCode() < 700 ? $e->getCode() : 500);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'errors' => [
                    'success' => false,
                    'requestAt' => now()->format('Y-m-d H:i:s'),
                    'httpStatus' => 'UNAUTHORIZED',
                    'message' => 'Invalid credentials',
                ]
            ], 401);
        }
        $user = Auth::user();

        // delete all tokens in the authenticated user | only single login account per device ❤️❤️❤️
        $user->tokens()->delete();

        $token = $user->createToken(
            Str::lower($credentials['email']) . '_auth_token', ['*'],
            now()->addDays(7)
        )->plainTextToken;

        return response()->json([
            'requestAt' => now()->format('Y-m-d H:i:s'),
            'success' => true,
            'token' => $token,
            'httpStatus' => 'OK',
            'message' => 'Login successfull, Welcome!',
            'data' => $user->only('name', 'email'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

}

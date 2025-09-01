<?php

namespace App\Components;

use Illuminate\Http\JsonResponse;

class ResponseFormat
{
    public static function success(string $message, mixed $data = null, int $status = 200): JsonResponse
    {
        return response()->json([
            'requestAt' => now()->format('Y-m-d H:i:s'),
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function creationSuccess(string $message, string $role, mixed $createdAt, mixed $data = null, int $status = 201): JsonResponse
    {
        return response()->json([
            'createdAt' => $createdAt->now()->format('Y-m-d H:i:s'),
            'message' => $message,
            'role' => $role,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'requestAt' => now()->format('Y-m-d H:i:s'),
            'message' => $message,
        ], $status);
    }


}



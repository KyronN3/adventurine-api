<?php

namespace App\Components;

use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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

    public static function error(string $message, int $status = 400): JsonResponse
    {
        return response()->json([
            'requestAt' => now()->format('Y-m-d H:i:s'),
            'message' => $message,
        ], $status);
    }

}



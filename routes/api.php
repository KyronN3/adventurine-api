<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecognitionController;
use Illuminate\Support\Facades\Route;

/*
    * Welcome Message
*/

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to City API! ❤️❤️❤️',
    ], 200);
});

/*
    * Authentication
*/

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('route-role-verifier');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Future: Strictly role base access here
    Route::prefix('/admin')->group(function () {
        Route::post('/recognition/create', [RecognitionController::class, 'createNewRecognition'])
            ->withoutMiddleware(['auth:sanctum', 'route-role-verifier']); // skip for testing only
    });
    Route::prefix('/recognition')->group(function () {
        Route::get('search/all', [RecognitionController::class, 'getRecognitions']);
        Route::get('search/{id}', [RecognitionController::class, 'getRecognitionById']);
        Route::get('search/department/{department}', [RecognitionController::class, 'getRecognitionsByDepartment']);
        Route::get('search/recent', [RecognitionController::class, 'getRecognitionRecent']);;
        Route::get('search/history', [RecognitionController::class, 'getRecognitionHistory']);;
    });
});



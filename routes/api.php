<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecognitionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BpmController;

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
        Route::prefix('/recognition')->group(function () {
            Route::post('/create', [RecognitionController::class, 'createNewRecognition']);
            Route::post('/delete/{id}', [RecognitionController::class, 'deletePendingRecognition']);
            Route::put('/approve/{id}', [RecognitionController::class, 'approveRecognition']);
            Route::put('/reject/{id}', [RecognitionController::class, 'rejectRecognition']);;
        });
    });

    Route::prefix('/recognition')->group(function () {
        Route::get('search/all', [RecognitionController::class, 'getRecognitions']);
        Route::get('search/history', [RecognitionController::class, 'getRecognitionHistory']);
        Route::get('search/{id}', [RecognitionController::class, 'getRecognitionById'])
            ->where('id', '[0-9]+');
        Route::get('search/department/{department}', [RecognitionController::class, 'getRecognitionsByDepartment']);
        Route::get('search/recent', [RecognitionController::class, 'getRecognitionRecent']);;
        Route::get('search/history', [RecognitionController::class, 'getRecognitionHistory']);;
    });

    
    Route::prefix('/hr')->group(function () {
        Route::post('/event/create', [EventController::class, 'createNewEvent']);
        Route::put('/event/{event}', [EventController::class, 'update']);
        Route::delete('/event/{event}', [EventController::class, 'destroy']);
    });

    
    Route::prefix('/event')->group(function () {
        Route::get('search/all', [EventController::class, 'getEvents']);
        Route::get('search/{id}', [EventController::class, 'getEventById']);
        Route::get('search/status', [EventController::class, 'getEventsByStatus']);
        Route::get('search/upcoming', [EventController::class, 'getUpcomingEvents']);
        Route::get('search/past', [EventController::class, 'getPastEvents']);
        Route::get('{event}', [EventController::class, 'show']);
    });

    // just read and creating. cuz frontend will handle the filtering - velvet underground 🍌
    // that didn't age quite well - velvet underground 🍌
    Route::prefix('/bpm')->group(function () {
        Route::get('', [BpmController::class, 'getBpm']);
        Route::post('/create', [BpmController::class, 'store']);
        Route::put('/{bpm}', [BpmController::class, 'update']);
        Route::get('/office/{office}/date/{date}', [BpmController::class, 'getBpmByOfficeAndDate']);
    });

    // Employee data routes
    Route::prefix('/employees')->group(function () {
        Route::get('/office/{office}', [BpmController::class, 'getEmployeesByOffice']);
        Route::get('/test', [BpmController::class, 'testDatabaseConnection']);
    });
});
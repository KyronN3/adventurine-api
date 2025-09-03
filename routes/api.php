<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BPMController;
use App\Http\Controllers\EmployeesAndOfficeController;
use App\Http\Controllers\EventController;
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
    * Getting Office Data 🙂
*/


Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    /*
    * Authentication 🔐
    */

    Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['auth:sanctum']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('route-role-verifier')->withoutMiddleware(['auth:sanctum']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // HR event routes ✍️
    Route::prefix('hr')->group(function () {
        Route::post('/event/create', [EventController::class, 'createNewEventStore']);
        Route::put('/event/{event}', [EventController::class, 'update']);
        Route::delete('/event/{event}', [EventController::class, 'destroy']);
    });

    // Event routes
    Route::prefix('/event')->group(function () {
        Route::get('search/all', [EventController::class, 'getEvents']);
        Route::get('verified', [EventController::class, 'getVerifiedEvents']);
        Route::get('search/{id}', [EventController::class, 'getEventById']);
        Route::get('search/status', [EventController::class, 'getEventsByStatus']);
        Route::get('search/upcoming', [EventController::class, 'getUpcomingEvents']);
        Route::get('search/past', [EventController::class, 'PastEvents']);
        Route::get('search', [EventController::class, 'searchEventsName']);
    });

    // BPM routes
    Route::prefix('/bpm')->group(function () {
        Route::get('', [BPMController::class, 'getBpm']);
        Route::post('/create', [BPMController::class, 'store']);
        Route::put('/{bpm}', [BPMController::class, 'update']);
        Route::get('/office/{office}/date/{date}', [BPMController::class, 'getBpmByOfficeAndDate']);
    });

    // Office Data
    Route::get('/office', [EmployeesAndOfficeController::class, 'getOffice'])->withoutMiddleware(['auth:sanctum']);

    // Employee data routes
    Route::prefix('/employees')->group(function () {
        Route::get('/office/{office}', [EmployeesAndOfficeController::class, 'getEmployeesByOffice']);
    });

});

/*
    * Below for no Auth Route ❗❗❗.
    * If you want the route to have Auth move the route above. Leave the v1 prefix ❗❗❗.
*/

Route::prefix('v1')->group(function () {
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
});



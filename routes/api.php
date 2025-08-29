<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RecognitionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;

/*
⠀⣞⢽⢪⢣⢣⢣⢫⡺⡵⣝⡮⣗⢷⢽⢽⢽⣮⡷⡽⣜⣜⢮⢺⣜⢷⢽⢝⡽⣝
⠸⡸⠜⠕⠕⠁⢁⢇⢏⢽⢺⣪⡳⡝⣎⣏⢯⢞⡿⣟⣷⣳⢯⡷⣽⢽⢯⣳⣫⠇
⠀⠀⢀⢀⢄⢬⢪⡪⡎⣆⡈⠚⠜⠕⠇⠗⠝⢕⢯⢫⣞⣯⣿⣻⡽⣏⢗⣗⠏⠀
⠀⠪⡪⡪⣪⢪⢺⢸⢢⢓⢆⢤⢀⠀⠀⠀⠀⠈⢊⢞⡾⣿⡯⣏⢮⠷⠁⠀⠀
⠀⠀⠀⠈⠊⠆⡃⠕⢕⢇⢇⢇⢇⢇⢏⢎⢎⢆⢄⠀⢑⣽⣿⢝⠲⠉⠀⠀⠀⠀
⠀⠀⠀⠀⠀⡿⠂⠠⠀⡇⢇⠕⢈⣀⠀⠁⠡⠣⡣⡫⣂⣿⠯⢪⠰⠂⠀⠀⠀⠀
⠀⠀⠀⠀⡦⡙⡂⢀⢤⢣⠣⡈⣾⡃⠠⠄⠀⡄⢱⣌⣶⢏⢊⠂⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⢝⡲⣜⡮⡏⢎⢌⢂⠙⠢⠐⢀⢘⢵⣽⣿⡿⠁⠁⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠨⣺⡺⡕⡕⡱⡑⡆⡕⡅⡕⡜⡼⢽⡻⠏⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⣼⣳⣫⣾⣵⣗⡵⡱⡡⢣⢑⢕⢜⢕⡝⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⣴⣿⣾⣿⣿⣿⡿⡽⡑⢌⠪⡢⡣⣣⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⡟⡾⣿⢿⢿⢵⣽⣾⣼⣘⢸⢸⣞⡟⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
⠀⠀⠀⠀⠁⠇⠡⠩⡫⢿⣝⡻⡮⣒⢽⠋⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀⠀
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


    
    // HR event routes
    Route::prefix('/hr')->group(function () {
        Route::post('/event/create', [EventController::class, 'createNewEvent']);
        Route::put('/event/{event}', [EventController::class, 'updateEvent']);
        Route::delete('/event/{event}', [EventController::class, 'deleteEventById']);
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
    Route::get('{event}', [EventController::class, 'show']);
});


});
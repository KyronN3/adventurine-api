<?php

use App\Components\enum\MinioBucket;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BpmController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EmployeesAndOfficeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MinioController;
use App\Http\Controllers\RecognitionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to City API! ❤️❤️❤️',
    ], 200);
});

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {

    // Authentication routes 🔐
    Route::prefix('/auth')->group(function () {
//        Route::post('/register', [AuthController::class, 'register'])->withoutMiddleware(['auth:sanctum']); //Uncomment this if you want to use register
        Route::post('/login', [AuthController::class, 'login'])->middleware('route-role-verifier')->withoutMiddleware(['auth:sanctum']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // HR routes ✍️
    Route::prefix('hr')->group(function () {
        // Event routes
        Route::prefix('/event')->group(function () {
            Route::post('/create', [EventController::class, 'createNewEvent']);
            Route::delete('/delete/{id}', [EventController::class, 'destroy']);
        });

        // Recognition routes
        Route::prefix('/recognition')->group(function () {
            Route::post('/delete/{id}', [RecognitionController::class, 'deletePendingRecognition']);
            Route::put('/approved/{id}', [RecognitionController::class, 'approveRecognition']);
            Route::put('/rejected/{id}', [RecognitionController::class, 'rejectRecognition']);
        });
    });

    // Admin routes ✍️
    Route::prefix('/admin')->group(function () {
        //Nominate Participant routes
        Route::prefix('/event')->group(function () {
            Route::post('/nominate', [EventController::class, 'nominateEventParticipant']);
            Route::match(['put', 'patch'], '/update/{id}', [EventController::class, 'updateEvent']);
        });

        // Recognition routes
        Route::prefix('/recognition')->group(function () {
            Route::post('/create', [RecognitionController::class, 'createRecognition']);

            Route::prefix('/file')->group(function () {
                Route::delete('/delete/{filename}/{filetype}', [MinioController::class, 'deleteByFileName']);
                Route::delete('/delete/batch', [MinioController::class, 'deleteBatch']);
            });
        });

        // BPM routes
        Route::prefix('/bpm')->group(function () {
            Route::post('/create', [BpmController::class, 'store']);
            Route::put('/{bpm}', [BpmController::class, 'update']);
        });
    });


    /* Global (Read-only) GET ❗🚫❗*/

    // Event routes
    Route::prefix('/event')->group(function () {
        Route::get('verified', [EventController::class, 'getVerifiedEvents']);
        Route::get('unverified', [EventController::class, 'getUnverifiedEvents']);
        Route::get('all', [EventController::class, 'getAllEvents']);
        Route::get('past', [EventController::class, 'getPastEvents']);
        Route::get('search/{id}', [EventController::class, 'getEventById']);
        Route::get('search/status', [EventController::class, 'getEventsByStatus']);
        Route::get('search/upcoming', [EventController::class, 'getUpcomingEvents']);
        Route::get('search', [EventController::class, 'searchEventsName']);
    });

    // Recognition routes
    Route::prefix('/recognition')->group(function () {
        Route::get('search/all', [RecognitionController::class, 'getRecognitions']);
        Route::get('search/history', [RecognitionController::class, 'getRecognitionHistory']);
        Route::get('search/{id}', [RecognitionController::class, 'getRecognitionById'])
            ->where('id', '[0-9]+');
        Route::get('search/department/{department}', [RecognitionController::class, 'getRecognitionsByDepartment'])
            ->where('department', '[A-Za-z\s\-]+');;
        Route::get('search/recent', [RecognitionController::class, 'getRecognitionRecent']);
        Route::get('search/media/{id}', [RecognitionController::class, 'getRecognitionMediaById'])
            ->where('id', '[0-9]+');;
    });

    // Bpm Routes
    Route::prefix('/bpm')->group(function () {
        Route::get('', [BpmController::class, 'getBpm']);
        Route::get('/office/{office}/date/{date}', [BpmController::class, 'getBpmByOfficeAndDate']);
    });

    // Office data routes
    Route::get('/office', [EmployeesAndOfficeController::class, 'getOffice']);

    // Employee data routes
    Route::prefix('/employees')->group(function () {
        Route::get('/office/{office}', [EmployeesAndOfficeController::class, 'getEmployeesByOffice']);
    });

    // Certificate
    Route::prefix('/certificate')->group(function () {
        Route::get('/recognition/id/{id}', [CertificateController::class, 'generateRecognitionCertificateById']);;
        Route::post('/recognition/generate', [CertificateController::class, 'generateRecognitionCertificate']);;
    });

    // Media query
    Route::prefix('/media')->group(function () {
//        Route::get('/{route}/{type}/{filename}', [MinioController::class, 'fetchFileNameV2'])
//            ->where('filename', '.*'); // <— this allows everything

        Route::get('/view/{filetype}/{filename}', [MinioController::class, 'fetchByFileName'])
            ->where('filename', '.*'); // <— this allows everything


        Route::get('/stream/{route}/{type}/{filename}', function ($route, $type, $filename) {
            try {
                // Pick the correct disk based on route and type
                $disk = match ($route) {
                    'recognition' => match ($type) {
                        'image' => Storage::disk(MinioBucket::RECOGNITION_IMAGE),
                        'file' => Storage::disk(MinioBucket::RECOGNITION_FILE),
                        default => throw new \InvalidArgumentException('Invalid type'),
                    },
                    'event' => match ($type) {
                        'image' => Storage::disk(MinioBucket::EVENT_IMAGE),
                        'file' => Storage::disk(MinioBucket::EVENT_FILE),
                        default => throw new \InvalidArgumentException('Invalid type'),
                    },
                    default => throw new \InvalidArgumentException('Invalid route'),
                };

                if (!$disk->exists($filename)) {
                    return response()->json(['message' => 'File not found'], 404);
                }

                // Stream the file
                return response()->stream(function () use ($disk, $filename) {
                    echo $disk->get($filename);
                }, 200, [
                    'Content-Type' => $disk->mimeType($filename),
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                    'Access-Control-Allow-Origin' => '*', // handle CORS
                ]);

            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
        });
    });
});

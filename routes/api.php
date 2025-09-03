<?php

use App\Components\enum\MinioBucket;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RecognitionController;
use App\Http\Controllers\MinioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;


/*
    * Welcome Message
*/

Route::get('/welcome', function () {
    return response()->json([
        'message' => 'Welcome to City API! ❤️❤️❤️',
    ], 200);
});


Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login'])->middleware('route-role-verifier');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    /*
        * Below for no Auth Route ❗❗❗
        * If you want the route to have Auth move the route above. Leave the v1 prefix ❗❗❗
    */

    // ✅ removed the nested v1
    Route::prefix('/admin')->group(function () {
        Route::prefix('/recognition')->group(function () {
            Route::post('/create', [RecognitionController::class, 'createNewRecognition']);
            Route::post('/delete/{id}', [RecognitionController::class, 'deletePendingRecognition']);
            Route::put('/approve/{id}', [RecognitionController::class, 'approveRecognition']);
            Route::put('/reject/{id}', [RecognitionController::class, 'rejectRecognition']);

            Route::prefix('/file')->group(function () {
                Route::get('/fetch/{filename}/{filetype}', [MinioController::class, 'fetchByFileName']);
                Route::delete('/delete/{filename}/{filetype}', [MinioController::class, 'deleteByFileName']);
                Route::delete('/delete/batch', [MinioController::class, 'deleteBatch']);
            });
        });
    });

    Route::prefix('/recognition')->group(function () {
        Route::get('search/all', [RecognitionController::class, 'getRecognitions']);
        Route::get('search/history', [RecognitionController::class, 'getRecognitionHistory']);
        Route::get('search/{id}', [RecognitionController::class, 'getRecognitionById'])
            ->where('id', '[0-9]+');
        Route::get('search/department/{department}', [RecognitionController::class, 'getRecognitionsByDepartment'])
            ->where('department', '[A-Za-z\s\-]+');;
        Route::get('search/recent', [RecognitionController::class, 'getRecognitionRecent']);
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

        Route::post('/certificate/generate', [CertificateController::class, 'generateRecognitionCertificate']);
    });


    Route::get('media/{route}/{type}/{filename}', [MinioController::class, 'fetchFileNameV2'])
        ->where('filename', '.*'); // <— this allows everything

    Route::get('media/stream/{route}/{type}/{filename}', function ($route, $type, $filename) {
        try {
            // Pick the correct disk based on route and type
            $disk = match ($route) {
                'recognition' => match ($type) {
                    'image' => Storage::disk(MinioBucket::RECOGNITION_IMAGE),
                    'file'  => Storage::disk(MinioBucket::RECOGNITION_FILE),
                    default => throw new \InvalidArgumentException('Invalid type'),
                },
                'event' => match ($type) {
                    'image' => Storage::disk(MinioBucket::EVENT_IMAGE),
                    'file'  => Storage::disk(MinioBucket::EVENT_FILE),
                    default => throw new \InvalidArgumentException('Invalid type'),
                },
                default => throw new \InvalidArgumentException('Invalid route'),
            };

            if (!$disk->exists($filename)) {
                return response()->json(['message' => 'File not found'], 404);
            }

            // Stream the file
            return response()->stream(function() use ($disk, $filename) {
                echo $disk->get($filename);
            }, 200, [
                'Content-Type' => $disk->mimeType($filename),
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Access-Control-Allow-Origin' => '*', // handle CORS
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    });


});



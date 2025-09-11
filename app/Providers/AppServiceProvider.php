<?php

namespace App\Providers;

use App\Http\Controllers\RecognitionController;
use App\Http\Requests\recognition\CertificateRecognitionRequest;
use App\Http\Requests\recognition\ICreateRecognitionRequest;
use App\Services\recognition\IRecognitionReadService;
use App\Services\recognition\RecognitionReadServiceV2;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        $this->app->bind(ICreateRecognitionRequest::class, CertificateRecognitionRequest::class);

        $this->app->when(RecognitionController::class)
            ->needs(IRecognitionReadService::class)
            ->give(RecognitionReadServiceV2::class);    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

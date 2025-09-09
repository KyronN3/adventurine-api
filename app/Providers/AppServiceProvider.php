<?php

namespace App\Providers;

use App\Http\Requests\recognition\CertificateRecognitionRequest;
use App\Http\Requests\recognition\ICreateRecognitionRequest;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

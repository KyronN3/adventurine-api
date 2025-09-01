<?php

namespace App\Components;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\enum\TrainingFunction;
use App\Components\enum\BpmFunction;
use Illuminate\Support\Facades\Log;

// FOR LOG DEBUGGING
// USE THIS FOR LOGGING YOUR FUNCTIONS
// IN DEVELOPMENT. CHANGES LATER
class LogMessages
{
    // FOR RECOGNITION
    public static function recognition(RecognitionFunction $function, LayerLevel $layer,  LogLevel $log): void
    {
        switch ($log) {
            case LogLevel::INFO:
                Log::info("Recognition " . $function->value . " successfully in " . $layer->value . " layer.");
                break;
            case LogLevel::ERROR:
                Log::error("Recognition " . $function->value . " failed in " . $layer->value . " layer.");
                break;
            case LogLevel::WARNING:
                Log::warning("Recognition " . $function->value . " warning in" . $layer->value . " layer.");
                break;
        }
    }


    public static function training(TrainingFunction $function, LogLevel $log, LayerLevel $layer): void
    {
        
        switch ($log) {
            case LogLevel::INFO:
                Log::info("Training " . $function->value . " successfully in " . $layer->value . " layer.");
                break;
            case LogLevel::ERROR:
                Log::error("Training " . $function->value . " failed in " . $layer->value . " layer.");
                break;
            case LogLevel::WARNING:
                Log::warning("Training " . $function->value . " warning" . $layer->value . " layer.");
                break;
        }
    }

    public static function bpm(BpmFunction $function, LayerLevel $layer, LogLevel $log): void
    {
        switch ($log) {
            case LogLevel::INFO:
                Log::info("BPM " . $function->name . " successfully in " . $layer->value . " layer.");
                break;
            case LogLevel::ERROR:
                Log::error("BPM " . $function->name . " failed in " . $layer->value . " layer.");
                break;
            case LogLevel::WARNING:
                Log::warning("BPM " . $function->name . " warning in " . $layer->value . " layer.");
                break;
        }
    }

}

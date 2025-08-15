<?php

namespace App\Components;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\enum\TrainingFunction;
use Illuminate\Support\Facades\Log;

// FOR LOG DEBUGGING
// USE THIS FOR LOGGING YOUR FUNCTIONS
// IN DEVELOPMENT. CHANGES LATER
class LogMessages
{
    // FOR RECOGNITION
    public static function recognition(RecognitionFunction $function, LayerLevel $layer,  LogLevel $log, $recognition = null): void
    {
        if ($recognition instanceof \Exception) {
            $context = [
                'message' => $recognition->getMessage(),
                'trace' => $recognition->getTraceAsString(),
            ];
        } else {
            $context = [
                'recognition_id' => $recognition->id ?? null,
                'employee_id' => $recognition->employee_id ?? null,
                'employee_name' => $recognition->employee_name ?? null,
            ];
        }

        switch ($log) {
            case LogLevel::INFO:
                Log::info("Recognition " . $function->value . " successfully in " . $layer->value . " layer.", $context || '');
                break;
            case LogLevel::ERROR:
                Log::error("Recognition " . $function->value . " failed in " . $layer->value . " layer.", $context || '');
                break;
            case LogLevel::WARNING:
                Log::warning("Recognition " . $function->value . " warning in" . $layer->value . " layer.", $context || '');
                break;
        }
    }


    public static function training(TrainingFunction $function, LogLevel $log, LayerLevel $layer, $training = null): void
    {
        if ($training instanceof \Exception) {
            $context = [
                'message' => $training->getMessage(),
                'trace' => $training->getTraceAsString(),
            ];
        } else {
            $context = [
                'recognition_id' => $training->id ?? null,
                'employee_id' => $training->employee_id ?? null,
                'employee_name' => $training->employee_name ?? null,
            ];
        }

        switch ($log) {
            case LogLevel::INFO:
                Log::info("Training " . $function->value . " successfully in " . $layer->value . " layer.", $context);
                break;
            case LogLevel::ERROR:
                Log::error("Training " . $function->value . " failed in " . $layer->value . " layer.", $context);
                break;
            case LogLevel::WARNING:
                Log::warning("Training " . $function->value . " warning" . $layer->value . " layer.", $context);
                break;
        }
    }

}

<?php

namespace App\Services;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Exceptions\RecognitionServiceException;
use App\Models\Recognition;

class RecognitionService
{
    /**
     * @throws RecognitionServiceException
     */
    // Creates new recognition
    // Overrides status and date
    // Save to DB using Model ORM
    // Console logs after action
    // Returns recognition id if success, throw otherwise
    public function createNewRecognition(array $data): string
    {
        // Override status and date submitted
        $data['status'] = $data['status'] ?? 'pending';
        $data['date_submitted'] = $data['date_submitted'] ?? now();

        try {
            $recognition = Recognition::create($data);
            LogMessages::recognition(RecognitionFunction::CREATION, LogLevel::INFO, LayerLevel::SERVICE, $recognition);

            return (string)$recognition->id;
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::CREATION, LogLevel::ERROR, LayerLevel::SERVICE, $e);
            throw new RecognitionServiceException(
                "Error creating new recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e);
        }
    }

    /**
     * @throws RecognitionServiceException
     */
    public function deletePendingRecognition($id): void
    {
        try {
            $recognition = Recognition::find($id);

            if (!$recognition) {
                throw new RecognitionServiceException("Recognition not found.");
            }

            if ($recognition->status != 'pending' || $recognition->status != 'PENDING') {
                throw new RecognitionServiceException("Recognition is not pending.");
            }

            $recognition->delete();
            LogMessages::recognition(RecognitionFunction::DELETE_PENDING, LogLevel::INFO, LayerLevel::SERVICE, $recognition);
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::DELETE_PENDING, LogLevel::ERROR, LayerLevel::SERVICE, $e);
            throw new RecognitionServiceException(
                "Error deleting pending recognition.",
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }
}

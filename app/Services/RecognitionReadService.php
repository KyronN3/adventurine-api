<?php

namespace App\Services;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Exceptions\RecognitionServiceException;
use App\Models\Recognition;
use App\Components\LogMessages;


class RecognitionReadService
{
    /**
     * @throws RecognitionServiceException
     */
    private function fetchRecognitions(array $filters = [], RecognitionFunction $function = RecognitionFunction::SEARCH_ALL): array
    {
        try {
            LogMessages::recognition($function, LayerLevel::SERVICE, LogLevel::INFO);

            $query = Recognition::query()
                ->select([
                    'id',
                    'status',
                    'hr_comment',
                    'date_submitted',
                    'employee_id',
                    'employee_department',
                    'employee_name',
                    'recognition_date',
                    'recognition_type',
                    'achievement_description'
                ]);

            // Apply filters dynamically
            foreach ($filters as $key => $value) {
                if (is_array($value)) {
                    $query->whereIn($key, $value);
                } else {
                    $query->where($key, $value);
                }
            }

            return $query->get()->toArray();

        } catch (\Exception $e) {
            throw new RecognitionServiceException(
                "Error fetching recognitions.",
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitions(): array
    {
        return $this->fetchRecognitions([]);
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionById($id): array
    {
        return $this->fetchRecognitions(['id' => $id], RecognitionFunction::SEARCH_BY_ID);
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionByDepartment($department): array
    {
        return $this->fetchRecognitions(['employee_department' => $department], RecognitionFunction::SEARCH_BY_DEPARTMENT);
    }

    /**
     * @throws RecognitionServiceException
     */
    public function getRecognitionHistory(): array
    {
        return $this->fetchRecognitions(['status' => ['approved', 'rejected']], RecognitionFunction::SEARCH_HISTORY);
    }

}

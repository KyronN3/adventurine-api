<?php

namespace App\Services;

class ResponseData
{
    public static function recognition($recognition, array $images = [], array $files = []): array
    {
        $imageUrls = array_map(fn($image) => [
            'id' => $image['id'] ?? '',
            'url' => $image['url'] ?? '',
            'expires' => $image['expires'] ?? '',
        ], $images);

        $fileUrls = array_map(fn($file) => [
            'id' => $file['id'] ?? '',
            'url' => $file['url'] ?? '',
            'expires' => $file['expires'] ?? '',
        ], $files);

        return [
            'id' => $recognition->id ?? '',
            'status' => $recognition->status ?? '',
            'hrComment' => $recognition->hr_comment ?? '',
            'dateSubmitted' => $recognition->date_submitted?->format('Y-m-d') ?? null,
            'employeeId' => $recognition->employee_id ?? '',
            'employeeDepartment' => $recognition->employee_department ?? '',
            'employeeName' => $recognition->employee_name ?? '',
            'recognitionDate' => $recognition->recognition_date?->format('Y-m-d'),
            'recognitionType' => $recognition->recognition_type ?? '',
            'achievementDescription' => $recognition->achievement_description ?? '',

            'imageUrls' => $imageUrls,
            'fileUrls' => $fileUrls,
        ];
    }
}

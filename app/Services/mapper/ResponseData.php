<?php

namespace App\Services\mapper;

namespace App\Services;

class ResponseData
{
    public static function recognition2($recognition): array
    {
        $imageNames = $recognition->images
            ->map(fn($img) => [
                'id' => $img->id ?? '',
                'originalName' => $img->original_name,
            ])
            ->toArray();

        $fileNames = $recognition->files
            ->map(fn($file) => [
                'id' => $file->id ?? '',
                'originalName' => $file->original_name,
            ])
            ->toArray();

        return [
            'id' => $recognition->id ?? '',
            'status' => $recognition->status ?? '',
            'hrComment' => $recognition->hr_comment ?? '',
            'dateSubmitted' => $recognition->date_submitted?->format('Y-m-d') ?? null,
            'employeeId' => $recognition->employee_id ?? '',
            'employeeDepartment' => $recognition->employee_department_clean ?? '',
            'employeeName' => $recognition->employee_name ?? '',
            'recognitionDate' => $recognition->recognition_date?->format('Y-m-d') ?? null,
            'recognitionType' => $recognition->recognition_type ?? '',
            'achievementDescription' => $recognition->achievement_description ?? '',
            'title' => $recognition->title ?? '',

            'images' => $imageNames,
            'files' => $fileNames,
        ];
    }

    public static function recognition($recognition, array $images = [], array $files = []): array
    {
        $imageUrls = array_map(fn($image) => [
            'id' => $image['id'] ?? '',
            'name' => $image['name'] ?? '',
            'url' => $image['url'] ?? '',
            'expires' => $image['expires'] ?? '',
        ], $images);

        $fileUrls = array_map(fn($file) => [
            'id' => $file['id'] ?? '',
            'name' => $file['name'] ?? '',
            'url' => $file['url'] ?? '',
            'expires' => $file['expires'] ?? '',
        ], $files);

        return [
            'id' => $recognition->id ?? '',
            'status' => $recognition->status ?? '',
            'hrComment' => $recognition->hr_comment ?? '',
            'dateSubmitted' => $recognition->date_submitted?->format('Y-m-d') ?? null,
            'employeeId' => $recognition->employee_id ?? '',
            'employeeDepartment' => $recognition->employee_department_clean ?? '',
            'employeeName' => $recognition->employee_name ?? '',
            'recognitionDate' => $recognition->recognition_date?->format('Y-m-d'),
            'recognitionType' => $recognition->recognition_type ?? '',
            'achievementDescription' => $recognition->achievement_description ?? '',
            'title' => $recognition->title ?? '',

            'images' => $imageUrls,
            'files' => $fileUrls,
        ];
    }

    public static function recognitionMedia($id, $expires, array $images = [], array $files = []): array
    {
        $imageUrls = array_map(fn($image) => [
            'originalName' => $image['originalName'] ?? '',
            'fileName' => $image['fileName'] ?? '',
            'url' => $image['url'] ?? '',
            'expires' => $image['expires'] ?? '',
        ], $images);

        $fileUrls = array_map(fn($file) => [
            'originalName' => $file['originalName'] ?? '',
            'fileName' => $file['fileName'] ?? '',
            'url' => $file['url'] ?? '',
            'expires' => $file['expires'] ?? '',
        ], $files);

        return [
            'id' => $id ?? '',
            'expires' => $expires ?? null,
            'images' => $imageUrls,
            'files' => $fileUrls,
        ];
    }

}

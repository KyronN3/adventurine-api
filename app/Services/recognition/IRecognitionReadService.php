<?php

namespace App\Services\recognition;

interface IRecognitionReadService
{
    public function getRecognitions(): array;

    public function getRecognitionById($id): array;

    public function getRecognitionByDepartment($department): array;

    public function getRecognitionHistory(): array;

    public function getRecognitionMediaById($id): array;
}

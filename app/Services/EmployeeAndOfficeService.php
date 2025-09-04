<?php

namespace App\Services;

use E;

class EmployeeAndOfficeService
{
    public function formatKey($key): array
    {
        $data = [];
        foreach ($key as $employee) {
            $format = [
                'controlno' => $employee->ControlNo,
                'name' => $employee->Name2,
                'office' => $employee->Office,
                'sex' => $employee->Sex,
                'designation' => $employee->Designation,
                'status' => $employee->Status,
            ];
            array_push($data, $format);
        }
        return $data;
    }
}

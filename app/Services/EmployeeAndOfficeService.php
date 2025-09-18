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
                'name' => $employee->Name4,
                'office' => $employee->Office,
                'officeShort' => str_replace('OFFICE OF THE CITY ', '', $employee->Office),
                'sex' => $employee->Sex,
                'designation' => $employee->Designation,
                'status' => $employee->Status,
            ];

            array_push($data, $format);
        }
        return $data;
    }
}

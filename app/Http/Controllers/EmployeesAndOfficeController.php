<?php

namespace App\Http\Controllers;

use App\Components\ResponseFormat;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmployeesAndOfficeController extends Controller
{
    public function getEmployeesByOffice(string $office): JsonResponse
    {
        try {
            $employees = Cache::remember('employee_' . $office, now()->addDay(), function () use ($office) {
                return DB::table('vwActive')->select([
                    'ControlNo',
                    'Name4',
                    'Office',
                    'Sex',
                    'Designation',
                    'Status'
                ])->where('Office', $office)->orderBy('Name4')->get();
            });

            if ($employees->isEmpty()) {
                return ResponseFormat::success('No employees found for this office', []);
            }
            return ResponseFormat::success('Employees retrieved successfully', $employees);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving employees: ' . $e->getMessage(), 500);
        }
    }

    public function getOffice()
    {
        try {
            $office = Cache::remember('office', now()->addDays(7), function () {
                return DB::table('vwofficearrangement')->pluck('Office');
            });

            if ($office->isEmpty()) {
                return ResponseFormat::success('No office found');
            }
            return ResponseFormat::success('Office retrieved successfully', $office);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving office: ' . $e->getMessage(), 500);
        }
    }

}

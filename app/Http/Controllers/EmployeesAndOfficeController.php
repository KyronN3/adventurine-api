<?php

namespace App\Http\Controllers;

use App\Components\ResponseFormat;
use App\Services\EmployeeAndOfficeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmployeesAndOfficeController extends Controller
{

    protected EmployeeAndOfficeService $employeeAndOfficeService;

    public function __construct(EmployeeAndOfficeService $employeeAndOfficeService)
    {
        $this->employeeAndOfficeService = $employeeAndOfficeService;
    }

    public function getEmployeesByOffice(string $office): JsonResponse
    {
        try {
            $key = [
                'ControlNo',
                'Name4',
                'Office', // Full office name
                DB::raw("REPLACE(Office, 'OFFICE OF THE CITY ', '') as OfficeShort"), // Cleaned office
                'Sex',
                'Designation',
                'Status'
            ];

            $employees = Cache::remember($office, now()->addDay(), function () use ($office, $key) {
                // add for is training 
                return DB::table('vwActive')->select($key)->where('Office', $office)->orderBy('Name4')->paginate(60);
            });

            if ($employees->isEmpty()) {
                return ResponseFormat::success('No employees found for this office', []);
            }

            return ResponseFormat::success('Employees retrieved successfully', $this->employeeAndOfficeService->formatKey($employees));
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

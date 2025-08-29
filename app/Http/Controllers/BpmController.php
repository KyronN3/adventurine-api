<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBPMRequest;
use App\Models\Bpm;
use App\Services\BpmService;
use App\Components\ResponseFormat;
use App\Exceptions\BpmServiceException;
use App\Components\enum\BpmFunction;
use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\LogMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BPMController extends Controller
{
    protected BpmService $service;
    
    public function __construct(BpmService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of all BPM records.
     */
    public function getBpm(): JsonResponse //shows all data
    {
        try {
            $bpms = $this->service->getAllBpms();
            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::INFO);
            return ResponseFormat::success('BPM records retrieved successfully', $bpms);
        } catch (\Exception $e) {
            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error retrieving BPM records: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created BPM record in storage.
     */
    public function store(StoreBPMRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            
            // checker just in case if non batch was requested
            if (isset($validatedData['bpm_entries'])) {
                // batch
                $response = $this->service->createMultipleBpms($validatedData['bpm_entries']);
                $message = count($response) . ' BPM records created successfully!';
            } else {
                $response = $this->service->createNewBpm($validatedData);
                $message = 'New BPM record created successfully!';
            }
            
            LogMessages::bpm(BpmFunction::CREATION, LayerLevel::CONTROLLER, LogLevel::INFO);
            return ResponseFormat::success($message, $response, 201);
        } catch (BpmServiceException $e) {
            LogMessages::bpm(BpmFunction::CREATION, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            LogMessages::bpm(BpmFunction::CREATION, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error creating new BPM record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update the specified BPM record in storage.
     */
    public function update(Request $request, Bpm $bpm): JsonResponse
    {
        try {
            \Log::info('Update BPM called:', ['id' => $bpm->id, 'request_data' => $request->all()]);

            // Get the raw request data
            $requestData = $request->all();

            // Extract bpm_entries if it exists
            if (isset($requestData['bpm_entries']) && count($requestData['bpm_entries']) > 0) {
                $bpmData = $requestData['bpm_entries'][0];

                // Validate the data manually for updates (more lenient)
                $validatedData = [
                    'control_no' => $bpmData['control_no'] ?? $bpm->control_no,
                    'medical_history' => $bpmData['medical_history'] ?? $bpm->medical_history,
                    'bpm_systolic' => $bpmData['bpm_systolic'] ?? $bpm->bpm_systolic,
                    'bpm_diastolic' => $bpmData['bpm_diastolic'] ?? $bpm->bpm_diastolic,
                    'bpm_dateTaken' => $bpmData['bpm_dateTaken'] ?? $bpm->bpm_dateTaken
                ];

                \Log::info('Updating BPM with data:', $validatedData);

                $bpm->update($validatedData);

                LogMessages::bpm(BpmFunction::UPDATE, LayerLevel::CONTROLLER, LogLevel::INFO);
                return ResponseFormat::success('BPM record updated successfully!', $bpm);
            } else {
                \Log::error('No bpm_entries found in update request');
                LogMessages::bpm(BpmFunction::UPDATE, LayerLevel::CONTROLLER, LogLevel::ERROR);
                return ResponseFormat::error('Invalid data provided for update', 400);
            }
        } catch (BpmServiceException $e) {
            LogMessages::bpm(BpmFunction::UPDATE, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            \Log::error('Error in update method:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            LogMessages::bpm(BpmFunction::UPDATE, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error updating BPM record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified BPM record from storage.
     */
    public function destroy(Bpm $bPM): JsonResponse
    {
        try {
            $bPM->delete();
            LogMessages::bpm(BpmFunction::DELETE, LayerLevel::CONTROLLER, LogLevel::INFO);
            return ResponseFormat::success('BPM record deleted successfully!');
        } catch (\Exception $e) {
            LogMessages::bpm(BpmFunction::DELETE, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error deleting BPM record: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get employees by office from vwActive view.
     */
    public function getEmployeesByOffice(string $office): JsonResponse
    {
        try {
            // First, let's check if the view exists and get its structure
            try {
                $viewExists = DB::select("SHOW TABLES LIKE 'vwActive'");
                if (empty($viewExists)) {
                    \Log::error('vwActive view does not exist in database');
                    return ResponseFormat::error('vwActive view does not exist', 404);
                }

                // Get column information
                $columns = DB::select("DESCRIBE vwActive");
                \Log::info('vwActive columns:', $columns);
            } catch (\Exception $e) {
                \Log::error('Error checking vwActive view:', ['error' => $e->getMessage()]);
                return ResponseFormat::error('Database error: ' . $e->getMessage(), 500);
            }

            $employees = DB::table('vwActive')
                ->select([
                    'ControlNo',
                    'Name1',
                    'Office',
                    'Sex',
                    'Designation',
                    'Status'
                ])
                ->where('Office', $office)
                ->orderBy('Name1')
                ->get();

            \Log::info('Employees query result:', ['office' => $office, 'count' => $employees->count(), 'first' => $employees->first()]);

            // If no employees found, return empty array instead of error
            if ($employees->isEmpty()) {
                \Log::warning('No employees found for office:', ['office' => $office]);
                return ResponseFormat::success('No employees found for this office', []);
            }

            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::INFO);
            return ResponseFormat::success('Employees retrieved successfully', $employees);
        } catch (\Exception $e) {
            \Log::error('Error in getEmployeesByOffice:', ['office' => $office, 'error' => $e->getMessage()]);
            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error retrieving employees: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get BPM records by office and date.
     */
    public function getBpmByOfficeAndDate(string $office, string $date): JsonResponse
    {
        try {
            \Log::info('getBpmByOfficeAndDate called:', ['office' => $office, 'date' => $date]);

            $bpmRecords = DB::table('bpm')
                ->leftJoin('vwActive', 'bpm.control_no', '=', 'vwActive.ControlNo')
                ->select([
                    'bpm.id',
                    'bpm.control_no',
                    'bpm.medical_history',
                    'bpm.bpm_systolic',
                    'bpm.bpm_diastolic',
                    'bpm.bpm_dateTaken',
                    'vwActive.Name1 as employee_name',
                    'vwActive.Office as Office',
                    'vwActive.Sex as Sex',
                    'vwActive.Designation as Designation',
                    'vwActive.Status as Status'
                ])
                ->where('vwActive.Office', $office)
                ->where('bpm.bpm_dateTaken', $date)
                ->orderBy('vwActive.Name1')
                ->get();

            \Log::info('BPM records query result:', [
                'office' => $office,
                'date' => $date,
                'count' => $bpmRecords->count(),
                'first_record' => $bpmRecords->first(),
                'all_records' => $bpmRecords->toArray()
            ]);

            // Also check raw BPM table for debugging
            $rawBpmRecords = DB::table('bpm')
                ->where('bpm_dateTaken', $date)
                ->get();
            \Log::info('Raw BPM table records for date:', [
                'date' => $date,
                'count' => $rawBpmRecords->count(),
                'records' => $rawBpmRecords->toArray()
            ]);

            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::INFO);
            return ResponseFormat::success('BPM records retrieved successfully', $bpmRecords);
        } catch (\Exception $e) {
            \Log::error('Error in getBpmByOfficeAndDate:', [
                'office' => $office,
                'date' => $date,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            LogMessages::bpm(BpmFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::ERROR);
            return ResponseFormat::error('Error retrieving BPM records: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Test database connection and vwActive view.
     */
    public function testDatabaseConnection(): JsonResponse
    {
        try {
            // Test basic database connection
            $tables = DB::select('SHOW TABLES');
            $tableNames = array_column($tables, 'Tables_in_' . env('DB_DATABASE', 'laravel'));

            // Check if vwActive exists
            $vwActiveExists = in_array('vwActive', $tableNames);

            // Get BPM table info
            $bpmCount = DB::table('bpm')->count();

            // Try to query vwActive if it exists
            $vwActiveInfo = null;
            if ($vwActiveExists) {
                try {
                    $columns = DB::select('DESCRIBE vwActive');
                    $vwActiveCount = DB::table('vwActive')->count();
                    $sampleData = DB::table('vwActive')->limit(1)->first();

                    $vwActiveInfo = [
                        'exists' => true,
                        'columns' => $columns,
                        'count' => $vwActiveCount,
                        'sample' => $sampleData
                    ];
                } catch (\Exception $e) {
                    $vwActiveInfo = [
                        'exists' => true,
                        'error' => $e->getMessage()
                    ];
                }
            }

            return ResponseFormat::success('Database test completed', [
                'database_connected' => true,
                'tables_found' => count($tableNames),
                'bpm_records_count' => $bpmCount,
                'vwActive' => $vwActiveInfo ?: ['exists' => false]
            ]);

        } catch (\Exception $e) {
            return ResponseFormat::error('Database test failed: ' . $e->getMessage(), 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Components\enum\BpmFunction;
use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\LogMessages;
use App\Components\ResponseFormat;
use App\Exceptions\BpmServiceException;
use App\Http\Requests\StoreBPMRequest;
use App\Models\Bpm;
use App\Services\BpmService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpmController extends Controller
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
            $requestData = $request->all();

            if (isset($requestData['bpm_entries']) && count($requestData['bpm_entries']) > 0) {
                $bpmData = $requestData['bpm_entries'][0];

                $validatedData = [
                    'control_no' => $bpmData['control_no'] ?? $bpm->control_no,
                    'medical_history' => $bpmData['medical_history'] ?? $bpm->medical_history,
                    'bpm_systolic' => $bpmData['bpm_systolic'] ?? $bpm->bpm_systolic,
                    'bpm_diastolic' => $bpmData['bpm_diastolic'] ?? $bpm->bpm_diastolic,
                    'bpm_dateTaken' => $bpmData['bpm_dateTaken'] ?? $bpm->bpm_dateTaken
                ];

                $bpm->update($validatedData);
                return ResponseFormat::success('BPM record updated successfully!', $bpm);
            } else {
                return ResponseFormat::error('Invalid data provided for update', 400);
            }
        } catch (\Exception $e) {
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


    /**
     * Get BPM records by office and date.
     */
    public function getBpmByOfficeAndDate(string $office, string $date): JsonResponse
    {
        try {
            $bpmRecords = DB::table('ldrBpm')
                ->leftJoin('vwActive', 'ldrBpm.control_no', '=', 'vwActive.ControlNo')
                ->select([
                    'ldrBpm.id',
                    'ldrBpm.control_no',
                    'ldrBpm.medical_history',
                    'ldrBpm.bpm_systolic',
                    'ldrBpm.bpm_diastolic',
                    'ldrBpm.bpm_dateTaken',
                    'vwActive.Name4 as employee_name',
                    'vwActive.Office as Office',
                    'vwActive.Sex as Sex',
                    'vwActive.Designation as Designation',
                    'vwActive.Status as Status'
                ])
                ->where('vwActive.Office', $office)
                ->where('ldrBpm.bpm_dateTaken', $date)
                ->orderBy('vwActive.Name4')
                ->get();

            return ResponseFormat::success('BPM records retrieved successfully', $bpmRecords);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving BPM records: ' . $e->getMessage(), 500);
        }
    }

}

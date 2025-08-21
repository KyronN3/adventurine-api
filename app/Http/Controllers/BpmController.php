<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBPMRequest;
use App\Models\BPM;
use App\Services\BpmService;
use App\Components\ResponseFormat;
use App\Exceptions\BpmServiceException;
use App\Components\enum\BpmFunction;
use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\LogMessages;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    // no update cuz duh, why update your bpm? - velvet underground 🍌

    /**
     * Remove the specified BPM record from storage.
     */
    public function destroy(BPM $bPM): JsonResponse
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
}

<?php

namespace App\Http\Controllers;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Components\ResponseFormat;
use App\Exceptions\RecognitionServiceException;
use App\Http\Requests\CreateRecognitionRequest;
use App\Services\RecognitionService;
use Illuminate\Http\JsonResponse;


class RecognitionController extends Controller
{
    protected RecognitionService $service;

    public function __construct(RecognitionService $service)
    {
        $this->service = $service;
    }

    public function createNewRecognition(CreateRecognitionRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $response = $this->service->createNewRecognition($data);
            return ResponseFormat::success('New recognition created successfully!', $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::CREATION, LogLevel::ERROR, LayerLevel::CONTROLLER, $e);
            return ResponseFormat::error('Error creating new recognition.');
        }
    }

    public function deletePendingRecognition($id): JsonResponse
    {

        // future service class
        //  $deleted = RecognitionService.delete($id);


        return ResponseFormat::success('Recognition deleted successfully');
    }

    public function approveRecognition($id, string $hrComment): JsonResponse
    {
        return ResponseFormat::success('Recognition test approved successfully. HR comment: ' . $hrComment);
    }

    public function rejectRecognition($id): JsonResponse
    {
        return ResponseFormat::success('Recognition test rejected successfully');
    }


    /// READ-ONLY FUNCTIONS
    public function getRecognitions(): JsonResponse
    {
        return ResponseFormat::success('Recognition test search all successfully');
    }

    public function getRecognitionById($id): JsonResponse
    {
        return ResponseFormat::success('Recognition test search by id ' . $id . ' successfully');
    }

    public function getRecognitionsByDepartment($department): JsonResponse
    {
        return ResponseFormat::success('Recognition test search department ' . $department . ' successfully');

    }

    public function getRecognitionRecent(): JsonResponse
    {
        return ResponseFormat::success('Recognition test search recent successfully');
    }

    public function getRecognitionHistory(): JsonResponse
    {  // For Approved and Rejected
        return ResponseFormat::success('Recognition test search history successfully');
    }


    //
    //
    //
    // Less Priority
//    public function getPendingRecognitions(): JsonResponse {
//        return ResponseFormat::success('Recognition test approved successfully');
//    }
//
//    public function getApprovedRecognitions(): JsonResponse {
//        return ResponseFormat::success('Recognition test approved successfully');
//    }
//
//    public function getRejectedRecognitions(): JsonResponse {
//        return ResponseFormat::success('Recognition test approved successfully');
//    }

}

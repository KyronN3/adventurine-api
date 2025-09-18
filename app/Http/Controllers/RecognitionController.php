<?php

namespace App\Http\Controllers;

use App\Components\enum\LayerLevel;
use App\Components\enum\LogLevel;
use App\Components\enum\RecognitionFunction;
use App\Components\LogMessages;
use App\Components\ResponseFormat;
use App\Exceptions\RecognitionServiceException;
use App\Http\Requests\recognition\AcademicRecognitionRequest;
use App\Http\Requests\recognition\CertificateRecognitionRequest;
use App\Http\Requests\recognition\MilestoneRecognitionRequest;
use App\Http\Requests\RecognitionRequest;
use App\Services\recognition\IRecognitionReadService;
use App\Services\recognition\RecognitionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class RecognitionController extends Controller
{
    protected RecognitionService $service;
    protected IRecognitionReadService $readService;

    public function __construct(RecognitionService $service, IRecognitionReadService $readService)
    {
        $this->service = $service;
        $this->readService = $readService;
    }

    public function createRecognition(RecognitionRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            Log::info('data before log: ', $data);

            $response = $this->service->createNewRecognition($data);
            return ResponseFormat::success('New recognition created successfully!', $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::CREATION, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error creating new recognition.');
        }
    }

    // ==================================================================== //

    public function deletePendingRecognition($id): JsonResponse
    {
        try {
            $this->service->deletePendingRecognition($id);
            return ResponseFormat::success('Recognition deleted successfully');

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::DELETE_PENDING, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error deleting pending recognition.');
        }
    }

    public function approveRecognition($id, string $hrComment = null): JsonResponse
    {
        try {
            $this->service->approvePendingRecognition($id);
            return ResponseFormat::success('Recognition approved successfully');

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::APPROVES, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error approving pending recognition.');
        }
    }

    public function rejectRecognition($id, Request $request): JsonResponse
    {
        try {
            $hrComment = $request->input('hrComment', '');

            $this->service->rejectPendingRecognition($id, $hrComment);
            return ResponseFormat::success('Recognition rejected successfully');

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::REJECTS, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error rejecting pending recognition.');
        }
    }


    /// READ-ONLY FUNCTIONS
    public function getRecognitions(): JsonResponse
    {
        try {
            $response = $this->readService->getRecognitions();
            return ResponseFormat::success('All recognition fetched successfully', $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::SEARCH_ALL, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error($e->getMessage());
            //'Error fetching all recognition.'
        }
    }

    public function getRecognitionById($id): JsonResponse
    {
        try {
            $response = $this->readService->getRecognitionById($id);
            return ResponseFormat::success("Recognition id: {$id} fetched successfully", $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::SEARCH_BY_ID, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error("Error fetching recognition id: {$id}.");
        }
    }

    public function getRecognitionsByDepartment(string $department): JsonResponse
    {
        try {
            $response = $this->readService->getRecognitionByDepartment($department);
            return ResponseFormat::success("All recognition by department: '{$department}' fetched successfully", $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::SEARCH_BY_DEPARTMENT, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error("Error fetching recognition for {$department}.");
        }
    }

    public function getRecognitionHistory(): JsonResponse
    {   // For Approved and Rejected
        try {
            $response = $this->readService->getRecognitionHistory();
            return ResponseFormat::success("All recognition history fetched successfully", $response);

            // Catch exceptions. Return error response
        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::SEARCH_HISTORY, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error fetching recognition history.');
        }
    }


    public function getRecognitionMediaById($id): JsonResponse
    {
        try {
            $response = $this->readService->getRecognitionMediaById($id);
            return ResponseFormat::success("Recognition media fetched successfully", $response);

        } catch (RecognitionServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            LogMessages::recognition(RecognitionFunction::SEARCH_MEDIA, LayerLevel::CONTROLLER, LogLevel::ERROR, $e);
            return ResponseFormat::error('Error fetching recognition media.');
        }
    }

}

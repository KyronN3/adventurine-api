<?php

namespace App\Http\Controllers;

use App\Components\ResponseFormat;
use App\Exceptions\EventServiceException;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\NominateParticipantRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\EventService;
use App\Services\NominateParticipantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    protected EventService $service;
    protected NominateParticipantService $nominateParticipantService;

    public function __construct(EventService $service, NominateParticipantService $nominateParticipantService)
    {
        $this->service = $service;
        $this->nominateParticipantService = $nominateParticipantService;
    }

    public function searchEventsName(Request $request): JsonResponse
    {
        try {
            $searchTerm = $request->query('search', '');
            if (empty($searchTerm)) {
                return ResponseFormat::error('Search term is required', 400);
            }
            $events = $this->service->searchEvents($searchTerm);
            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 500);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error searching events: ' . $e->getMessage(), 500);
        }
    }

    public function createNewEvent(CreateEventRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->service->createNewEvent($validatedData);

//            broadcast(new CreateEventsNotif(
//                $response->event_name,
//                $response->event_description,
//                $response->event_date,
//                $response->event_venue,
//                Auth::user()->getRoleNames()
//            ));

            return ResponseFormat::creationSuccess('New event created successfully!',
                Auth::user()->hasRole('hr') ? 'hr' : 'admin',
                now(), $response, 201);

        } catch (\Exception $e) {
            return ResponseFormat::error('Error creating new event: ' . $e->getMessage(), 500);
        }
    }

    public function nominateEventParticipant(NominateParticipantRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->nominateParticipantService->nominateParticipant($validatedData);

            return ResponseFormat::creationSuccess('Nominated Participants successfully!', Auth::user()->hasRole('hr') ? 'hr' : 'admin', now(), $response, 201);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error nominating participants: ' . $e->getMessage(), 500);
        }
    }

    public function updateEvent(UpdateEventRequest $request, $id): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->service->updateEvent($validatedData, $id);

            return ResponseFormat::success('Event updated successfully!', $response);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error updating event: ' . $e->getMessage(), 500);
        }
    }

    public function getVerifiedEvents(): JsonResponse
    {
        try {
            $events = $this->service->getVerifiedEvents();

            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving verified events: ' . $e->getMessage(), 500);
        }
    }

    public function getUnverifiedEvents()
    {
        try {
            $events = $this->service->getUnverifiedEvents();

            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving unverified events: ' . $e->getMessage(), 500);
        }
    }

    public function getPastEvents(): JsonResponse
    {
        try {
            $events = $this->service->getPastEvents();
            return ResponseFormat::success('Past events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving past events: ' . $e->getMessage(), 500);
        }
    }

    public function getAllEvents(): JsonResponse
    {
        try {
            $events = $this->service->getAllEvents();
            return ResponseFormat::success('All events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error($e->getMessage(), 500);
        }
    }

    public function deleteEventById($id): JsonResponse
    {
        try {
            $this->service->deleteEvent($id);
            return ResponseFormat::success('Event deleted successfully!');
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error deleting event: ' . $e->getMessage(), 500);
        }
    }

    public function getEventsByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status', 'all');
            $events = $this->service->getEventsByStatus($status);
            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving events: ' . $e->getMessage(), 500);
        }
    }

    public function getUpcomingEvents(): JsonResponse
    {
        try {
            $events = $this->service->getUpcomingEvents();
            return ResponseFormat::success('Upcoming events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving upcoming events: ' . $e->getMessage(), 500);
        }
    }

    public function getEventById($id): JsonResponse
    {
        try {
            $event = $this->service->getEventById($id);
            return ResponseFormat::success('Event retrieved successfully', $event);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 404);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving event: ' . $e->getMessage(), 500);
        }
    }


}

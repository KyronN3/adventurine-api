<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use App\Services\EventService;
use App\Components\ResponseFormat;
use App\Exceptions\EventServiceException;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    protected EventService $service;
    
    public function __construct(EventService $service)
    {
        $this->service = $service;
    }

   
    public function getEvents(): JsonResponse
    {
        try {
            $events = $this->service->getAllEvents();
            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving events: ' . $e->getMessage(), 500);
        }
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

   public function show(Event $event): JsonResponse
{
    return ResponseFormat::success('Event retrieved successfully', $event);
}
   
    public function createNewEvent(CreateEventRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->service->createNewEvent($validatedData);
            return ResponseFormat::success('New event created successfully!', $response, 201);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error creating new event: ' . $e->getMessage(), 500);
        }
    }

   
public function updateEvent(UpdateEventRequest $request, Event $event): JsonResponse
{
 
    try {
            $validatedData = $request->validated();

           
            if (empty($validatedData['event_name'])) {
                return ResponseFormat::error('Please provide a valid event name.', 422);
            }
            if (empty($validatedData['event_date'])) {
                return ResponseFormat::error('Please provide a valid event date (YYYY-MM-DD) format.', 422);
            }
            if (empty($validatedData['event_description'])) {
                return ResponseFormat::error('Please provide a valid event description.', 422);
            }
            if (empty($validatedData['event_venue'])) {
                return ResponseFormat::error('Please provide a valid event venue.', 422);
            }
            if (empty($validatedData['event_mode'])) {
                return ResponseFormat::error('Please provide a valid event mode.', 422);
            }
            if (empty($validatedData['event_activity'])) {
                return ResponseFormat::error('Please provide a valid event activity.', 422); 
                  
           }
            if (empty($validatedData['event_tags'])) {
                return ResponseFormat::error('Please provide at least one event tag.', 422);
            }
            if (empty($validatedData['event_departments'])) {
                return ResponseFormat::error('Please provide at least one event department.', 422);
            }
            if (empty($validatedData['event_forms'])) {
                return ResponseFormat::error('Please provide at least one event form.', 422);
            }
            $response = $this->service->updateEvent($event->id, $validatedData);
            return ResponseFormat::success('Event updated successfully!', $response);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error updating event: ' . $e->getMessage(), 500);
        }
}

  

    public function deleteEventById( $id): JsonResponse
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
   
    public function PastEvents(): JsonResponse
    {
        try {
            $events = $this->service->getPastEvents();
            return ResponseFormat::success('Past events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving past events: ' . $e->getMessage(), 500);
        }
    }

   
    public function checkDuplicateEvent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'event_name' => 'required|string|max:255',
                'event_date' => 'required|date_format:Y-m-d',
                'event_venue' => 'nullable|string|max:255',
            ]);

            $eventName = $request->input('event_name');
            $eventDate = $request->input('event_date');
            $eventVenue = $request->input('event_venue');

            $result = $this->service->checkEventExists($eventName, $eventDate, $eventVenue);

            if ($result['exists']) {
                return ResponseFormat::error($result['message'], 409, [
                    'existing_event' => $result['event'],
                    'duplicate_check' => true
                ]);
            }

            return ResponseFormat::success('No duplicate event found. You can proceed with creating this event.', [
                'duplicate_check' => false
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return ResponseFormat::error('Validation failed: ' . $e->getMessage(), 422);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error checking for duplicate events: ' . $e->getMessage(), 500);
        }
    }
}

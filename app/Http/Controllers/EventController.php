<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\Request;
use App\Services\EventService;
use App\Components\ResponseFormat;
use App\Exceptions\EventServiceException;
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
            return ResponseFormat::error('Error retrieving events: ' . $e->getMessage());
        }
    }
    
   
    public function getEventById($id): JsonResponse
    {
        try {
            $event = $this->service->getEventById($id);
            if (!$event) {
                return ResponseFormat::error('Event not found', 404);
            }
            return ResponseFormat::success('Event retrieved successfully', $event);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving event: ' . $e->getMessage());
        }
    }
    
    
    public function createNewEvent(CreateEventRequest $request): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->service->createNewEvent($validatedData);
            return ResponseFormat::success('New event created successfully!', $response);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            return ResponseFormat::error('Error creating new event: ' . $e->getMessage());
        }
    }

    
    public function store(CreateEventRequest $request): JsonResponse
    {
        return $this->createNewEvent($request);
    }

  
    public function show(Event $event): JsonResponse
    {
        try {
            $eventData = $this->service->getEventById($event->id);
            return ResponseFormat::success('Event retrieved successfully', $eventData);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving event: ' . $e->getMessage());
        }
    }

   
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $response = $this->service->updateEvent($event->id, $validatedData);
            return ResponseFormat::success('Event updated successfully!', $response);
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            return ResponseFormat::error('Error updating event: ' . $e->getMessage());
        }
    }

   
    public function destroy(Event $event): JsonResponse
    {
        try {
            $this->service->deleteEvent($event->id);
            return ResponseFormat::success('Event deleted successfully!');
        } catch (EventServiceException $e) {
            return ResponseFormat::error($e->getMessage());
        } catch (\Exception $e) {
            return ResponseFormat::error('Error deleting event: ' . $e->getMessage());
        }
    }
    
   
    public function getEventsByStatus(Request $request): JsonResponse
    {
        try {
            $status = $request->query('status', 'all');
            $events = $this->service->getEventsByStatus($status);
            return ResponseFormat::success('Events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving events: ' . $e->getMessage());
        }
    }
    
   
    public function getUpcomingEvents(): JsonResponse
    {
        try {
            $events = $this->service->getUpcomingEvents();
            return ResponseFormat::success('Upcoming events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving upcoming events: ' . $e->getMessage());
        }
    }
    
    
     
     
    public function getPastEvents(): JsonResponse
    {
        try {
            $events = $this->service->getPastEvents();
            return ResponseFormat::success('Past events retrieved successfully', $events);
        } catch (\Exception $e) {
            return ResponseFormat::error('Error retrieving past events: ' . $e->getMessage());
        }
    }
}

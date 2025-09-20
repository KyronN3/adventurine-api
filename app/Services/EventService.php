<?php

namespace App\Services;

use App\Exceptions\EventServiceException;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class EventService
{
    public function getEventById($id)
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->find($id);

        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve event: ' . $e->getMessage());
        }
    }

    public function getVerifiedEvents()
    {
        try {
            return event::with(['outcomes', 'attendance', 'participants'])
                ->where('event_verify', 'verified')->orderBy('event_schedule', 'desc')->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve verified events: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function getUnverifiedEvents()
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->where('event_verify', 'unverified')->orderBy('event_schedule', 'desc')->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve unverified events: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function getPastEvents()
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->whereIn('event_verify', ['verified', 'unverified'])
                ->whereRaw("JSON_VALUE(event_schedule, '$[0].date') < ?", [now()->format('Y-m-d')])
                ->orderByRaw("JSON_VALUE(event_schedule, '$[0].date') DESC")
                ->get();
        } catch (\Exception $e) {

            throw new EventServiceException('Failed to retrieve past events: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function getAllEvents()
    {
        try {
            $event = Event::all(['id', 'event_schedule', 'event_location', 'event_description', 'event_name']);
            return array_map(static function ($event) {
                return [
                    'id' => $event['id'],
                    'title' => $event['event_name'],
                    'location' => $event['event_location'],
                    'description' => $event['event_description'],
                    'schedule' => $event['event_schedule'],
                ];
            }, $event->toArray());

        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve all events: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function getEventsByStatus($status)
    {
        try {
            $query = Event::with(['outcomes', 'attendance', 'participants']);
            if ($status !== 'all') {
                if (!in_array($status, ['active', 'verified', 'completed', 'cancelled'])) {
                    throw new EventServiceException('Invalid status provided. Allowed values are: active, verified, completed, cancelled.');
                }
                $query->where('event_status', $status);
            }

            $events = $query->orderBy('event_date', 'desc')->get();

            return $events;
        } catch (\Exception $e) {

            throw new EventServiceException('Failed to retrieve events by status: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function createNewEvent($data)
    {
        try {
            DB::beginTransaction();
            $existingEvent = Event::where('event_name', $data['event_name'])
                ->where('event_schedule', $data['event_schedule'])
                ->where('event_location', $data['event_location'])
                ->first();

            if ($existingEvent) {
                DB::rollBack();
                throw new EventServiceException(
                    'Event already exists with the same name, date, and venue. Please check the existing event or modify your event details.'
                    , '', 409);
            }

            $sameNameDateEvent = Event::where('event_name', $data['event_name'])
                ->where('event_schedule', $data['event_schedule'])
                ->first();

            if ($sameNameDateEvent) {
                DB::rollBack();
                throw new EventServiceException(
                    'An event with the same name and date already exists. Please choose a different name or date.'
                    , '', 409);
            }

            $data['event_status'] = $data['event_status'] ?? 'active';

            $event = Event::create($data);

            DB::commit();

            return $event;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new EventServiceException('Failed to create event: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function updateEvent($data, $id)
    {
        try {
            $event = Event::find($id);
            DB::transaction(function () use ($data, $event) {
                if (!isset($event)) {
                    throw new EventServiceException('Event does not exist.');
                }
                $event->update($data);
            });

            return $event->load('participants');
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to update event: ' . $e->getMessage(), '', $e->getCode(), $e->getPrevious());
        }
    }

    public function deleteEvent($id)
    {
        try {
            DB::beginTransaction();


            $event = Event::with(['outcomes', 'attendance', 'participants'])->find($id);

            if (!$event) {
                throw new EventServiceException('Event not found.');
            }

            if ($event->event_status === 'active') {
                throw new EventServiceException('Cannot delete an active event. Please cancel or complete the event before deletion.');

            }
            $event->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new EventServiceException('Failed to delete event: ' . $e->getMessage());
        }
    }

    public function getUpcomingEvents()
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->where('event_date', '>=', now()->format('Y-m-d'))
                ->where('event_status', 'active')
                ->orderBy('event_date', 'asc')
                ->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve upcoming events: ' . $e->getMessage());
        }
    }

    public function searchEvents($searchTerm)
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->where(function ($query) use ($searchTerm) {
                    $query->where('event_name', 'like', "%{$searchTerm}%")
                        ->orWhere('event_description', 'like', "%{$searchTerm}%")
                        ->orWhere('event_venue', 'like', "%{$searchTerm}%");
                })
                ->orderBy('event_date', 'desc')
                ->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to search events: ' . $e->getMessage());
        }
    }

    public function checkEventExists($eventName, $eventDate, $eventVenue = null)
    {
        try {
            $query = Event::where('event_name', $eventName)
                ->where('event_date', $eventDate);

            if ($eventVenue) {
                $query->where('event_venue', $eventVenue);
            }

            $existingEvent = $query->first();

            if ($existingEvent) {
                return [
                    'exists' => true,
                    'event' => $existingEvent,
                    'message' => $eventVenue
                        ? 'Event already exists with the same name, date, and venue.'
                        : 'An event with the same name and date already exists.'
                ];
            }

            return [
                'exists' => false,
                'event' => null,
                'message' => 'No duplicate event found.'
            ];
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to check for existing events: ' . $e->getMessage());
        }
    }
}

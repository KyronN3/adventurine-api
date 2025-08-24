<?php

namespace App\Services;

use App\Models\Event;
use App\Exceptions\EventServiceException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventService
{
    
    public function getAllEvents()
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->orderBy('event_date', 'desc')
                ->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve events: ' . $e->getMessage());
        }
    }

   
    public function getEventById($id)
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->find($id);
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve event: ' . $e->getMessage());
        }
    }

   
    public function createNewEvent($data)
    {
        try {
            DB::beginTransaction();
            
            
            $existingEvent = Event::where('event_name', $data['event_name'])
                ->where('event_date', $data['event_date'])
                ->where('event_venue', $data['event_venue'])
                ->first();
            
            if ($existingEvent) {
                DB::rollBack();
                throw new EventServiceException('Event already exists with the same name, date, and venue. Please check the existing event or modify your event details.');
            }
            
            
            $sameNameDateEvent = Event::where('event_name', $data['event_name'])
                ->where('event_date', $data['event_date'])
                ->first();
            
            if ($sameNameDateEvent) {
                DB::rollBack();
                throw new EventServiceException('An event with the same name and date already exists. Please choose a different name or date.');
            }
            
            $data['event_created'] = now()->format('Y-m-d');
            $data['event_status'] = $data['event_status'] ?? 'active';
            
            $event = Event::create($data);
            
            DB::commit();
            
            return $event->load(['outcomes', 'attendance', 'participants']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new EventServiceException('Failed to create event: ' . $e->getMessage());
        }
    }

   
    public function updateEvent($id, $data)
    {
        try {
            DB::beginTransaction();
            
            $event = Event::findOrFail($id);
            $event->update($data);
            
            DB::commit();
            
            return $event->load(['outcomes', 'attendance', 'participants']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw new EventServiceException('Failed to update event: ' . $e->getMessage());
        }
    }

   
    public function deleteEvent($id)
    {
        try {
            DB::beginTransaction();
            

             $event=Event::with(['outcomes', 'attendance', 'participants']) ->find($id);

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

   /*
                        .......................... 
                 ................................... 
              ......................................... 
            ............................................. 
           ................................................ 
          .................................................. 
         .................................................... 
         ......;%;%%%%%%%%%%%%%%%%%%%%%%%%%%%;%%.............. 
         .....;%%%;;;;%%%%%%%%%%%%%%%%%%;;;;%%%%..............% 
         .....%%%%%%%%;;;%%%%%%%%%%%%;;;%%%%%%%%%............%%% 
         /....%%%%%%%%%%%%;%%%%%%%%;%%%%%%%%%%%%%%..........;%%% 
         //...%%%a@@`  '@%%//%%%%%%%%@`  '@@a%%%%%%........;%/%% 
         //...%@@@@@aaa@@@%//%%%%%%@@@@aaa@@@@@%%%%%......%%/%% 
         //...%%%%%%%%%%%%%//%%%%%%%%%%%%%%%%%%%%%%%%....%%/%%% 
          //..%%%%%%%%%%%%//%%%%%%%%%%%%%%%%%%%%%%%%%...%%/%%% 
           //.%%%%%%%%%%%%//%%%%%%%%%%%%%%%%%%%%%%%%%..%%/%%% 
            //%%%%%%%%%%%//%%%%%%%%%%%%%%%%%%%%%%%%%%..%/%%% 
             ;%%%%%%%%%%%//%%%%%%%%%;/%%%%%%%%%%%%%%%.%%% 
               %%%%%%%%%//%%%%%%%%%%%;/%%%%%%%%%%%%%%%% 
                %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%/ 
                 ;%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%// 
                   %%%%%<<<<<<<<<<<<<<<<<%%%%%%%%%%;// 
                    %%%%%<<<<<<<<<<<<<<<%%%%%%%%%%;/// 
                     %%%%%%%%%%%%%%%%%%%%%%%%%%%;///// 
                      %%%%%%%%%%%%%%%%%%%%%%%%;///////. 
                      /;%%%%%%%%%%%%%%%%%%%;////////.... 
                      ///;%%%%%%%%%%%%%%;////////......... 
                    ...///////////////////////.............. 
                  ........////////////////................,;;, 
               ,;............/////////.................,;;;;;;;;, 
           ,;;;;;;,................................,;;;;;;;;;;;;;;, 
       ,;;;;;;;;;;;;;,........................,;;;;;;;;;;;;;;;;;;;; 
   ,;;;;;;;;;;;;;;;;;;;;;,................,;;;;;;;;;;;;;;;;;;;;;;;; 
 ,;;;;;;;;;;;;;;;;;;;;;;;;;;,.........,;;;;;;;;;;;;;;;;;;;;;;;;;;;; 
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;/#\;;;;;;;;;;; 
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;/####\;;;;;;;;; 
;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;/#######\;;;;;;;
*/
    public function getEventsByStatus($status)
    {
        try {
            $query = Event::with(['outcomes', 'attendance', 'participants']);
            
            if ($status !== 'all') {
                $query->where('event_status', $status);
            }
            
            return $query->orderBy('event_date', 'desc')->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve events by status: ' . $e->getMessage());
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

   
    public function getPastEvents()
    {
        try {
            return Event::with(['outcomes', 'attendance', 'participants'])
                ->where('event_date', '<', now()->format('Y-m-d'))
                ->orderBy('event_date', 'desc')
                ->get();
        } catch (\Exception $e) {
            throw new EventServiceException('Failed to retrieve past events: ' . $e->getMessage());
        }
    }

    
public function searchEvents($searchTerm)
{
    try {
        return Event::with(['outcomes', 'attendance', 'participants'])
            ->where(function($query) use ($searchTerm) {
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

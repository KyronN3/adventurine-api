<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\EventService;

class TestEventAPI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:event-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Event API functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing EventService...');

        try {
            $eventService = new EventService();
            
            // Test creating an event
            $eventData = [
                'event_name' => 'Test Training Event',
                'event_description' => 'This is a test training event for API testing',
                'event_date' => '2024-12-25',
                'event_venue' => 'Conference Room A',
                'event_mode' => 'In-person',
                'event_activity' => 'Training',
                'event_tags' => ['training', 'test'],
                'event_departments' => ['IT', 'HR'],
                'event_status' => 'active'
            ];
            
            $this->info('Creating test event...');
            $createdEvent = $eventService->createNewEvent($eventData);
            $this->info("Event created successfully! ID: " . $createdEvent->id);
            
            // Test getting all events
            $this->info('Getting all events...');
            $allEvents = $eventService->getAllEvents();
            $this->info("Found " . count($allEvents) . " events");
            
            // Test getting event by ID
            $this->info('Getting event by ID...');
            $eventById = $eventService->getEventById($createdEvent->id);
            $this->info("Event found: " . $eventById->event_name);
            
            // Test getting upcoming events
            $this->info('Getting upcoming events...');
            $upcomingEvents = $eventService->getUpcomingEvents();
            $this->info("Found " . count($upcomingEvents) . " upcoming events");
            
            // Test getting past events
            $this->info('Getting past events...');
            $pastEvents = $eventService->getPastEvents();
            $this->info("Found " . count($pastEvents) . " past events");
            
            // Test getting events by status
            $this->info('Getting active events...');
            $activeEvents = $eventService->getEventsByStatus('active');
            $this->info("Found " . count($activeEvents) . " active events");
            
            // Test updating event
            $this->info('Updating event...');
            $updateData = [
                'event_name' => 'Updated Test Training Event',
                'event_description' => 'This is an updated test training event'
            ];
            $updatedEvent = $eventService->updateEvent($createdEvent->id, $updateData);
            $this->info("Event updated successfully! New name: " . $updatedEvent->event_name);
            
            // Test deleting event
            $this->info('Deleting test event...');
            $deleted = $eventService->deleteEvent($createdEvent->id);
            $this->info("Event deleted successfully!");
            
            $this->info("\nAll tests passed! ✅");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
        }
    }
}

<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class CreateEventsNotif implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected string $eventName;
    protected string $eventDescription;
    protected Carbon $eventDate;
    protected string $eventVenue;
    protected string $roles;

    public function __construct(string $eventName,
                                string $eventDescription,
                                string $eventDate,
                                string $eventVenue,
                                string $roles)
    {
        $this->$eventName = $eventName;
        $this->$eventDescription = $eventDescription;
        $this->$eventDate = Carbon::parse($eventDate);
        $this->$eventVenue = $eventVenue;
        $this->$roles = $roles;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('CreateEventNotification.' . $this->roles),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'eventName' => $this->eventName,
            'eventDescription' => $this->eventDescription,
            'eventDate' => $this->eventDate->format('Y-m-d'),
            'eventVenue' => $this->eventVenue,
        ];
    }

    public function broadcastWhen(): bool
    {
        return $this->eventDate->isFuture();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $table = 'ldrEvents';
    protected $fillable = [
        'event_name',
        'event_tags',
        'event_description',
        'event_departments',
        'event_date',
        'event_activity',
        'event_venue',
        'event_mode',
        'event_forms',
        'event_created',
        'event_status',
    ];

    protected $casts = [
        'event_tags' => 'array',
        'event_departments' => 'array',
        'event_forms' => 'array',
        'event_date' => 'date',
        'event_created' => 'date',
    ];

    public function outcomes()
    {
        return $this->hasMany(EventOutcome::class, 'event_id', 'id');
    }

    public function attendance()
    {
        return $this->hasMany(EventAttendance::class, 'event_id', 'id');
    }

    public function participants()
    {
        return $this->hasMany(EventParticipant::class, 'event_id', 'id');
    }


}




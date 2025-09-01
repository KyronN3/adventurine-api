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
        'event_types',
        'event_duration',
        'event_description',
        'event_departments',
        'event_date',
        'event_end_date',
        'event_location',
        'event_model',
        'event_forms',
        'event_status',
        'event_verify',
    ];

    protected $casts = [
        'event_types' => 'array',
        'event_departments' => 'array',
        'event_forms' => 'array',
        'event_date' => 'date:Y-m-d ',
        'event_end_date' => 'date:Y-m-d ',
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




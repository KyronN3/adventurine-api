<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    use HasFactory;

    protected $table = 'ldrEvent_attendance';

    protected $fillable = [
        'event_id',
        'name',
        'email',
        'status',
        'check_in',
        'notes',
    ];

    protected $casts = [
        'check_in' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

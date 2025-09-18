<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $table = 'ldrEvent_participants';

    protected $fillable = [
        'event_id',
        'employee_control_no',
        'employee_name',
        'event_name',
        'is_training'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

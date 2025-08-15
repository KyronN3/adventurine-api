<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventParticipant extends Model
{
    use HasFactory;

    protected $table = 'event_participants';

    protected $fillable = [
        'event_id',
        'participant_id',
        'name',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}

<?php

namespace App\Models\recognition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionMilestone extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_milestone';

    protected $fillable = [
        'recognition_id',
        'milestone',
        'milestone_date'
    ];

    protected $casts = [
        'milestone_date' => 'date'
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

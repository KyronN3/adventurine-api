<?php

namespace App\Models\recognition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionAcademic extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_academic';

    protected $fillable = [
        'recognition_id',
        'degree',
        'institution',
        'completion_date',
    ];

    protected $casts = [
        'completion_date' => 'date'
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

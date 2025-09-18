<?php

namespace App\Models\recognition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionImage extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_images';
    protected $fillable = [
        'id',
        'event_id',
        'original_name',
        'image_name',
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

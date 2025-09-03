<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionImage extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_images';
    protected $fillable = [
        'id',
        'event_id',
        'image_name',
    ];

    public function event()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

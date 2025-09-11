<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionFile extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_files';
    protected $fillable = [
        'id',
        'recognition_id',
        'original_name',
        'file_name',
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

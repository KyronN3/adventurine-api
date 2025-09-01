<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionFile extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognitionFiles';
    protected $fillable = [
        'id',
        'recognition_id',
        'file_name',
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }
}

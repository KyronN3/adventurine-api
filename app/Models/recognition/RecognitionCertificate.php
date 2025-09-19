<?php

namespace App\Models\recognition;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecognitionCertificate extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognition_certification';

    protected $fillable = [
        'recognition_id',
        'name',
        'citation',
        'title',
        'description',
        'issue',
    ];

    public function recognition()
    {
        return $this->belongsTo(Recognition::class, 'recognition_id', 'id');
    }

}

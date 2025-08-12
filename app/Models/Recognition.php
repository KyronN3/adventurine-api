<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recognition  extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'status',
        'hr_comment',
        'date_submitted',
        'employee_id',
        'employee_department',
        'employee_name',
        'recognition_date',
        'recognition_type',
        'achievement_description',
    ];


    public function files()
    {
        return $this->hasMany(RecognitionFile::class, 'recognition_id', 'id');
    }

    public function image()
    {
        return $this->hasMany(RecognitionImage::class, 'recognition_id', 'id');
    }

}

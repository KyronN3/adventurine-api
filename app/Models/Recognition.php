<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recognition extends Model
{
    use HasFactory;

    protected $table = 'ldrRecognitions'; // your DB table name

    protected $fillable = [
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

    protected $casts = [
        'date_submitted' => 'date',
        'recognition_date' => 'date'
    ];

    public function files()
    {
        return $this->hasMany(RecognitionFile::class, 'recognition_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(RecognitionImage::class, 'recognition_id', 'id');
    }


    public function toApproved(): static
    {
        return $this->setAttribute('status', 'approved');
    }

    public function toRejected(): static
    {
        return $this->setAttribute('status', 'rejected');
    }

    public function isPending(): bool
    {
        return strtolower($this->getAttribute('status')) === 'pending';
    }

    public function isApproved(): bool
    {
        return strtolower($this->getAttribute('status')) === 'approved';
    }

    public function isRejected(): bool
    {
        return strtolower($this->getAttribute('status')) === 'rejected';
    }

}

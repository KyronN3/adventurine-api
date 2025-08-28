<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bpm extends Model
{
    use HasFactory;

    // laravel sets the table name to 'b_p_m_s', which is ugly. this fix that - velvet underground 🍌
    protected $table = 'bpm';

    protected $fillable = [
        // subject to change if mag add na ang employee table - velvet underground 🍌
        'employee_name',
        'designation',
        'sex',
        'medical_history',
        'status',
        'bpm_systolic',
        'bpm_diastolic',
        'bpm_dateTaken'
    ];

    protected $casts = [
        'bpm_dateTaken' => 'date',
    ];
}

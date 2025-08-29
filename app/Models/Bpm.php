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
        'control_no',
        'employee_name',
        'designation',
        'sex',
        'medical_history',
        'status',
        'employee_department',
        'bpm_systolic',
        'bpm_diastolic',
        'bpm_dateTaken'
    ];

    protected $casts = [
        'bpm_dateTaken' => 'date',
        'control_no' => 'string',
    ];

    /**
     * Get the employee details from vwActive view
     */
    public function employee()
    {
        return $this->belongsTo(\Illuminate\Support\Facades\DB::table('vwActive'), 'control_no', 'ControlNo');
    }
}

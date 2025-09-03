<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'vwActive';   // reference their view
    public $timestamps = false;      // avoid Laravel timestamp expectations
    protected $primaryKey = 'ControlNo';
    public $incrementing = false;
    protected $keyType = 'string';   // or 'int' if numeric IDs
    protected $guarded = [];         // prevents accidental writes
}

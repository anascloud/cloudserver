<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'hr_employees';

    protected $fillable = [
        'employeeName', 'employeeManagerId', 'company_id', 'company_name',
        'location', 'capacity', 'inUseCapacity', 'emergencyContact',
        'binLocation', 'zoningLocation', 'startHour', 'endHour',
        'pickedBy', 'packedBy', 'quantityToPick', 'datePicked',
        'temperatureControlled', 'fireSafetyCompliance', 'status',
    ];

    protected $casts = [
        'employeeManagerId'    => 'integer',
        'company_id'           => 'integer',
        'capacity'             => 'integer',
        'inUseCapacity'        => 'integer',
        'quantityToPick'       => 'integer',
        'temperatureControlled'=> 'boolean',
        'fireSafetyCompliance' => 'boolean',
        'status'               => 'boolean',
        'datePicked'           => 'datetime:Y-m-d H:i:s',
    ];
}

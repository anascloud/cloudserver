<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetMovement extends Model
{
    use HasFactory;

    protected $fm_asset_movements = 'fm_asset_movements';
    protected $ = ["asset_movement_serial_number","company_id","transaction_date","purpose_of_movement","asset_id","asset_location_id","from_employee_id","from_employee_name","to_employee_id","to_employee_name","targeted_location_id","targeted_location_name"];


}

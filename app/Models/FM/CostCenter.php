<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;

    protected $fm_cost_centers = 'fm_cost_centers';
    protected $ = ["cost_center_name","company_id","is_active"];
}

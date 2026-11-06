<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCenter extends Model
{
    use HasFactory;

    protected $table = 'fm_cost_centers';
    protected $fillable = ["cost_center_name","company_id","is_active"];
}

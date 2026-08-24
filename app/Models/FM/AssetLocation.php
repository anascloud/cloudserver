<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
    use HasFactory;

    protected $fm_asset_locations = 'fm_asset_locations';
    protected $ = ["asset_location_name"];
}

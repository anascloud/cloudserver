<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
    use HasFactory;

    protected $table = 'fm_asset_locations';
    protected $fillable = ["asset_location_name"];
}

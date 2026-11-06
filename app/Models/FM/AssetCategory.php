<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $table = 'fm_asset_categories';
    protected $fillable = ["asset_category_name","fixed_asset_account_id"];
}

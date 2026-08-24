<?php

namespace App\Models\FM;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    use HasFactory;

    protected $fm_asset_depreciations = 'fm_asset_depreciations';
    protected $ = ["asset_depreciation_serial_number","asset_id","finance_book_id","finance_book_name","company_id","depreciation_method","total_depreciation_period","frequency_of_depreciation","expected_value","asset_status"];

    public function details()
    {
        return $this->hasMany($this->getDetailClass(), $this->getForeignKey());
    }
}
